<?php
/*
MO Reader, 
thanks to: Thomas Urban,
http://blog.toxa.de/archives/56
*/

/**
 * Improved MO file reader for Wordpress.
 *
 * @author Thomas Urban <thomas.urban@toxa.de>
 */

function mo_reader( $filename, $translate ) {

	/**
	 * read header from file
	 */

	$file = fopen( $filename, 'r' );
	if ( !$file )
		return $translate;

	$header = fread( $file, 28 );
	if ( strlen( $header ) != 28 )
		return $translate;

	// detect endianess
	$endian = unpack( 'Nendian', substr( $header, 0, 4 ) );
	$endian = dechex($endian['endian']);
	if (strlen($endian) == 16) $endian = substr( $endian, 8, 8 );
	
	if ( $endian == '950412de' )
		$endian = 'N';
	else if ( $endian == 'de120495' )
		$endian = 'V';
	else
		return $translate;

	// parse header
	$header = unpack( "{$endian}Hrevision/{$endian}Hcount/{$endian}HposOriginals/{$endian}HposTranslations/{$endian}HsizeHash/{$endian}HposHash", substr( $header, 4 ) );
	if ( !is_array( $header ) )
		return $translate;

	extract( $header );

	// support revision 0 of MO format specs, only
	if ( $Hrevision != 0 )
		return $translate;

	/*
	 * read index tables on originals and translations
	 */

	// seek to data blocks
	fseek( $file, $HposOriginals, SEEK_SET );

	// read originals' indices
	$HsizeOriginals = $HposTranslations - $HposOriginals;
	if ( $HsizeOriginals != $Hcount * 8 )
		return $translate;

	$originals = fread( $file, $HsizeOriginals );
	if ( strlen( $originals ) != $HsizeOriginals )
		return $translate;

	// read translations' indices
	$HsizeTranslations = $HposHash - $HposTranslations;
	if ( $HsizeTranslations != $Hcount * 8 )
		return $translate;

	$translations = fread( $file, $HsizeTranslations );
	if ( strlen( $translations ) != $HsizeTranslations )
		return $translate;

	// transform raw data into set of indices
	$originals    = str_split( $originals, 8 );
	$translations = str_split( $translations, 8 );

	/*
	 * read set of strings to separate string
	 */

	// skip hash table
	$HposStrings = $HposHash + $HsizeHash * 4;

	fseek( $file, $HposStrings, SEEK_SET );

	// read strings expected in rest of file
	$strings = '';
	while ( !feof( $file ) )
		$strings .= fread( $file, 4096 );

	fclose( $file );



	// collect hash records
	$hash = $header = array();

	for ( $i = 0; $i < $Hcount; $i++ )
	{

		// parse index records on original and related translation
		$o = unpack( "{$endian}length/{$endian}pos", $originals[$i] );
		$t = unpack( "{$endian}length/{$endian}pos", $translations[$i] );

		if ( !$o || !$t )
			return $translate;

		// adjust offset due to reading strings to separate space before
		$o['pos'] -= $HposStrings;
		$t['pos'] -= $HposStrings;

		// extract original and translations
		$original    = substr( $strings, $o['pos'], $o['length'] );
		$translation = substr( $strings, $t['pos'], $t['length'] );



		if ( $original === '' )
		{
			// got header --> store separately

			$header = array();

			foreach ( explode( "\n", $translation ) as $line )
			{

				$sep = strpos( $line, ':' );
				if ( $sep !== false )
					$header[trim(substr( $line, 0, $sep ))] = trim( substr( $line, $sep + 1 ));

			}
		}
		else
		{

			// detect context in original
			$sep = strpos( $original, "\04" );
			if ( $sep !== false )
			{
				$context  = substr( $original, 0, $sep );
				$original = substr( $original, $sep + 1 );
			}
			else
				$context  = null;


			$original     = explode( "\00", $original );
			$translation  = explode( "\00", $translation );

			$singularFrom = array_shift( $original );
			$singularTo   = array_shift( $translation );

			if ( count( $original ) && ( count( $original ) == count( $translation ) ) )
				$plurals = array_combine( $original, $translation );
			else
				$plurals = array();


			$record = array(
							'context'     => $context,
							'singular'    => $singularFrom,
							'translation' => $singularTo,
							'plurals'     => $plurals,
							);

			$key = is_null( $context ) ? $singularFrom
									   : "$context\04$singularFrom";


			$hash[$key] = $record;

		}
	}


	//print_r($header);
	if (isset($hash[$translate]['translation'])) return $hash[$translate]['translation'];
	
	return '';

}
?>