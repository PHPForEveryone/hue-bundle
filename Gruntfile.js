module.exports = function (grunt) {
    'use strict';
    var rootPath           = '../../',
        relativeOutputPath = 'web/';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        less: {
            production: {
                options: {
                    cleancss: true,
                    plugins: [
                        new (require('less-plugin-autoprefix'))({
                            browsers: ["last 2 versions"]
                        }),
                        new (require('less-plugin-clean-css'))({
                            advanced: true,
                            compatibility: 'ie9'
                        })
                    ]
                },
                files: {
                    'Resources/public/src/css/HueBundle.min.css': 'Resources/public/src/less/all.less'
                }
            }
        },
        watch: {
            scripts: {
                files: '**/*.less',
                tasks: ['less', 'shell'],
                options: {
                    debounceDelay: 250
                }
            }
        },
        shell: {
            install_theme_assets: {
                command: rootPath + 'bin/console assets:install ' + rootPath + relativeOutputPath,
                options: {
                    wait: false
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // These plugins provide necessary tasks.
    require('load-grunt-tasks')(grunt);

    // Default tasks and start watcher
    grunt.registerTask('default', [
        'less',
        'shell:install_theme_assets',
        'watch'
    ]);
};
