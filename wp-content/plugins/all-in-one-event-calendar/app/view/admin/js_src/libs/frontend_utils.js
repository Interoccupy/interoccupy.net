/**
* This modules defines some common functions that are used by some other frontend modules
*/
define( function() {
	"use strict"; // jshint ;_;
	/**
	 * Used to ensure that entities used in L10N strings are correct.
	 */
	var ai1ec_convert_entities = function( o ) {
		var c, v;

		c = function( s ) {
			if( /&[^;]+;/.test( s ) ) {
				var e = document.createElement( 'div' );
				e.innerHTML = s;
				return ! e.firstChild ? s : e.firstChild.nodeValue;
			}
			return s;
		};

		if( typeof o === 'string' ) {
			return c( o );
		} else if( typeof o === 'object' ) {
			for( v in o ) {
				if( typeof o[v] === 'string' ) {
					o[v] = c( o[v] );
				}
			}
		}
		return o;
	};

	/**
	 * Convert URI to map object
	 *
	 * @param {string} uri       URI to parse
	 * @param {char}   separator Character that separates arguments
	 * @param {char}   assigner  Character that denotes key from value
	 *
	 * @return {Object} Map of URI properties (non recursive!)
	 */
	var ai1ec_tokenize_uri = function( uri, separator, assigner ) {
		var argv, argc, key, value, spos;
		if ( '#' === uri.charAt( 0 ) || '?' === uri.charAt( 0 ) ) {
			uri = uri.substring( 1 );
		}
		argv = {};
		uri  = uri.split( separator );
		for ( argc = 0; argc < uri.length; argc++ ) {
			value = uri[argc].trim();
			if ( -1 !== ( spos = value.indexOf( assigner ) ) ) {
				key   = value.substring( 0, spos ).trim();
				value = value.substring( spos + 1 ).trim();
			} else {
				key   = value;
				value = true;
			}
			argv[key] = value;
		}
		return argv;
	};

	/**
	 * Parse internal query to more appropriate format.
	 *
	 * @param {string} hash Query hash to process
	 *
	 * @return {string} Converted query to use in admin-ajax request
	 */
	var ai1ec_map_internal_query = function( hash ) {
		var query, argc, keys, use_key, result;
		hash  = ai1ec_tokenize_uri( hash, '&', '=' );
		keys  = Object.keys( hash );
		query = {
			ai1ec  : {},
			action : 'posterboard'
		};
		for ( argc = 0; argc < keys.length; argc++ ) {
			if ( 'ai1ec' === keys[argc] ) {
				var new_map = ai1ec_tokenize_uri( hash[keys[argc]], '|', ':' );
				for ( use_key in new_map ) {
					if ( '' !== new_map[use_key] ) {
						if ( 'action' === use_key || 'view' === use_key ) {
							query.action = new_map[use_key];
						}
						query.ai1ec[use_key] = new_map[use_key];
					}
				}
			} else if ( 'ai1ec_' === keys[argc].substring( 0, 6 ) ) {
				query.ai1ec[keys[argc].substring( 6 )] = hash[keys[argc]];
			} else {
				query[keys[argc]] = hash[keys[argc]];
			}
		}
		if ( 'ai1ec_' !== query.action.substring( 0, 6 ) ) {
			query.action = 'ai1ec_' + query.action;
		}
		result = 'action=' + query.action + '&ai1ec=';
		for ( use_key in query.ai1ec ) {
			if( query.ai1ec.hasOwnProperty( use_key ) ) {
				result += escape( use_key ) + ':' + escape( query.ai1ec[use_key] ) + '|';
			}
		}
		result = result.substring( 0, result.length - 1 );
		for ( use_key in query ) {
			if ( 'ai1ec' !== use_key && 'action' !== use_key ) {
				result += '&' + use_key + '=' + escape( query[use_key] );
			}
		}
		return result;
	};

	return {
		ai1ec_convert_entities   : ai1ec_convert_entities,
		ai1ec_map_internal_query : ai1ec_map_internal_query,
		ai1ec_tokenize_uri       : ai1ec_tokenize_uri
	};
} );
