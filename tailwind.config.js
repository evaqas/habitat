module.exports = {
    purge: [
        './assets/{js,vue}/**/*.{js,vue}',
        './!(node_modules|vendor)/**/*.{php,twig}',
    ],
    theme: {
        extend: {},
    },
    variants: {},
    plugins: [],
}
