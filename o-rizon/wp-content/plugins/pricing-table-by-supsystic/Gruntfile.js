// TODO: for debbug JS files need to use $_GET['is_debbug'] == true
module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        uglify: {
            options: {
                mangle: false
            },
            dist: {
                files: {
                    'js/table.min.js': ['js/responsiveText.js'
                        , 'modules/tables/js/frontend.tables.editor.blocks_fabric.base.js'
                        , 'modules/tables/js/frontend.tables.editor.blocks.base.js'
                        , 'modules/tables/js/frontend.tables.editor.elements.base.js'
                    ]
                }
            }
        },

        cssmin: {
            css:{
                src: 'modules/tables/css/frontend.tables.css',
                dest: 'modules/tables/css/frontend.tables.min.css'
            }
        }
    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    // Default task(s).
    grunt.registerTask('default', ['uglify', 'cssmin']);
};
