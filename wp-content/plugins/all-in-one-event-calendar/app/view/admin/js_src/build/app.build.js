({
	appDir: "../",
	baseUrl: "./",                // This is relative to appDir
	dir: "../../js",              // This is relative to this file's dir
	keepBuildDir: false,
	fileExclusionRegExp: /(jasmine|build|jshintr|^\.|.bat|.sh$)/,
	mainConfigFile: '../main.js', // This is relative to this file's dir
	// optimize: "none",          // Uncomment this line if you need to debug
	modules: [
		{
			name: "scripts/calendar",
			// jquery must stay outside otherwise we overwrite wordpress version
			exclude : ["jquery"]
		},
		{
			name: "scripts/event",
			exclude : ["jquery"]
		},
		{
			name: "scripts/calendar_feeds",
			exclude : ["jquery"]
		},
		{
			name: "scripts/admin_settings",
			exclude : ["jquery"]
		},
		{
			name: "scripts/add_new_event",
			exclude : ["jquery"]
		},
		{
			name: "scripts/event_category",
			exclude : ["jquery"]
		},
		{
			name: "scripts/front_end_create_event_form",
			exclude : ["jquery"]
		},
		{
			name: "themes/vortex/scripts/calendar",
			exclude : ["jquery"]
		},
		{
			name: "main_widget",
			exclude : ["jquery"]
		},
		{
			name: "scripts/less_variables_editing",
			exclude : ["jquery"]
		},
		{
			name: "scripts/common_scripts/backend/common_backend",
			exclude : ["jquery"]
		},
		{
			name: "scripts/common_scripts/frontend/common_frontend",
			exclude : ["jquery"]
		}
	],
	namespace: 'timely', // Set the namespace.
	paths: {
		"ai1ec_calendar" : "empty:", // This modules are created dynamically in WP
		"ai1ec_config"   : "empty:",
		"jquery" : "require_jquery"
	},
	wrap: false,
	removeCombined: true
})
