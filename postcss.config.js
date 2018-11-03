const tailwindcss  = require('tailwindcss');
const purgecss     = require('@fullhuman/postcss-purgecss');
const autoprefixer = require('autoprefixer');

module.exports = {
    plugins: [
        tailwindcss( './tailwind.js' ),
        ... process.env.npm_lifecycle_event === 'build' ? [ purgecss( {
            content: [ './src/*.html' ],
            extractors: [
                {
                    extractor: class {
                        static extract( content ) {
                            return content.match(/[A-Za-z0-9-_:\/]+/g) || [];
                        }
                    },
                    extensions: [ 'html', 'php', 'js', 'jsx', 'vue', ],
                },
            ],
        } ) ] : [],
        autoprefixer
    ],
};
