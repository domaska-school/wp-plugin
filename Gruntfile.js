module.exports = function(grunt) {
	var path = require('path'),
		gc = {
			default: [
				"clean:all",
				"concat",
				"uglify",
				"less",
				"autoprefixer",
				"group_css_media_queries",
				"cssmin",
				"compress"
			]
		},
		PACK = grunt.file.readJSON('package.json'),
		banner = `/**
 * 
 * ${PACK.description}
 * Version:     ${PACK.version}
 * Author:      ${PACK.author}
 * Last Update: ${grunt.template.today("yyyy-mm-dd")}
 * 
 */
`;

	require('load-grunt-tasks')(grunt);

	grunt.initConfig({
		globalConfig : gc,
		pkg : PACK,
		clean: {
			options: {
				force: true
			},
			all: [
				'domcad/js/',
				'domcad/css/',
				'test/'
			]
		},
		concat: {
			options: {
				separator: "\n",
			},
			appjs: {
				src: [
					'bower_components/js-cookie/src/js.cookie.js',
					"bower_components/fancybox/src/js/core.js",
					// обработка ссылок на видео
					// YouTube, RUTUBE, Viemo
					'src/media.js',
					"bower_components/fancybox/src/js/guestures.js",
					"bower_components/fancybox/src/js/slideshow.js",
					"bower_components/fancybox/src/js/fullscreen.js",
					"bower_components/fancybox/src/js/thumbs.js",
					"bower_components/fancybox/src/js/hash.js",
					"bower_components/fancybox/src/js/wheel.js",
				],
				dest: 'domcad/js/jquery.fancybox.js'
			},
			main: {
				options: {
					separator: "\n",
					banner: banner,
				},
				src: [
					'bower_components/slick-carousel/slick/slick.js',
					'src/main.js'
				],
				dest: 'domcad/js/main.js'
			},
			sortable: {
				src: [
					'src/sortable.js'
				],
				dest: 'domcad/js/sortable.js'
			},
			files: {
				options: {
					separator: "\n",
					banner: banner,
				},
				src: [
					'src/file-ajax.js'
				],
				dest: 'domcad/js/file-ajax.js'
			},
			option: {
				options: {
					separator: "\n",
					banner: banner,
				},
				src: [
					'src/options.js'
				],
				dest: 'domcad/js/options.js'
			},
			css: {
				src: [
					'bower_components/fancybox/src/css/*.css'
				],
				dest: 'test/css/jquery.fancybox.css'
			}
		},
		uglify: {
			options: {
				sourceMap: false,
				compress: {
					drop_console: false
	  			},
	  			output: {
					ascii_only: true
				},
				banner: banner
			},
			app: {
				files: [
					{
						expand: true,
						flatten : true,
						src: [
							'domcad/js/jquery.fancybox.js',
							'domcad/js/main.js'
						],
						dest: path.normalize(path.join(__dirname, 'domcad', 'js')),
						filter: 'isFile',
						rename: function (dst, src) {
							return path.normalize(path.join(dst, src.replace('.js', '.min.js')));
						}
					},
					{
						expand: true,
						flatten : true,
						src: [
							'src/sortable.js',
						],
						dest: path.normalize(path.join(__dirname, 'domcad', 'js')),
						filter: 'isFile',
						rename: function (dst, src) {
							return path.normalize(path.join(dst, src.replace('.js', '.min.js')));
						}
					},
					{
						expand: true,
						flatten : true,
						src: [
							'src/file-ajax.js',
							'src/options.js'
						],
						dest: path.normalize(path.join(__dirname, 'domcad', 'js')),
						filter: 'isFile',
						rename: function (dst, src) {
							return path.normalize(path.join(dst, src.replace('.js', '.min.js')));
						}
					},
				]
			}
		},
		less: {
			css: {
				options : {
					compress: false,
					ieCompat: false,
					banner: banner,
					modifyVars: {
						"slick-font-path": "../fonts/",
						"slick-loader-path": "../images/",
					}
				},
				files : {
					'test/css/admin.css' : [
						'src/admin.less'
					],
					'test/css/main.css' : [
						'bower_components/slick-carousel/slick/slick.less',
						'bower_components/slick-carousel/slick/slick-theme.less',
						'src/main.less'
					],
				}
			}
		},
		autoprefixer: {
			options: {
				browsers: [
					"last 5 version"
				],
				cascade: true
			},
			css: {
				files: {
					'test/css/main.css' : [
						'test/css/main.css'
					],
					'test/css/admin.css' : [
						'test/css/admin.css'
					],
					'test/css/jquery.fancybox.css' : [
						'test/css/jquery.fancybox.css'
					],
				}
			}
		},
		group_css_media_queries: {
			group: {
				files: {
					'domcad/css//main.css': [
						'test/css/main.css'
					],
					'domcad/css//admin.css': [
						'test/css/admin.css'
					],
					'domcad/css//jquery.fancybox.css': [
						'test/css/jquery.fancybox.css'
					]
				}
			}
		},
		cssmin: {
			options: {
				mergeIntoShorthands: false,
				roundingPrecision: -1,
				banner: banner
			},
			minify: {
				files: {
					'domcad/css/main.min.css' : ['domcad/css/main.css'],
					'domcad/css/admin.min.css' : ['domcad/css/admin.css'],
					'domcad/css/jquery.fancybox.min.css' : ['domcad/css/jquery.fancybox.css']
				}
			}
		},
		compress: {
			main: {
				options: {
					archive: 'domcad.zip'
				},
				files: [
					{
						expand: true,
						cwd: '.',
						src: [
							'domcad/**',
						],
						dest: ''
					}
				],
			},
		},
	});
	grunt.registerTask('default',	gc.default);
};