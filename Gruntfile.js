module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({

    uglify: {
      options: {
        banner: '/*! <%= grunt.template.today("yyyy-mm-dd") %> */\n',
        mangle: true
      },
      frontend : {
        files: {
          "js/dist/script.min.js" : [ "js/script.js" ]
        },
        options: {
          // sourceMap: 'js/dist/source-map.js'
        }
      }
    }, // uglify


    // cssmin: {
    //   videojs: {
    //     src: 'video-js.css',
    //     dest: 'video-js.min.css'
    //   }
    // },


    sass : {
      dev: {
        files: {
          'css/screen.css': 'css/sass/screen.scss'
        },
        options: {
          debugInfo   :true,
          lineNumbers :true,
          trace       : true
        }
      },//dev

      frontend: {
        files: {
          'css/screen.min.css' : 'css/sass/screen.scss'
        },
        options: {
          style:'compressed'
        }
      }//frontend

    }// sass
  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-sass');

  // grunt.loadNpmTasks('grunt-img');

  grunt.registerTask('default', ['uglify', 'sass']);
  grunt.registerTask('js', ['uglify']);
  grunt.registerTask('css', ['sass']);

};
