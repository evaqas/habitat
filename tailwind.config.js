module.exports = {
    content: [
        './assets/{js,vue,svg}/**/*.{js,vue,svg}',
        './**/*.{php,twig}',
        './!**(dist|node_modules|vendor)',
    ],
    theme: {
        extend: {},
        screens: {
            '-xs': { max: '424px' },
             'xs': '425px',
            '-sm': { max: '599px' },
             'sm': '600px',
            '-md': { max: '767px' },
             'md': '768px',
            '-lg': { max: '1023px' },
             'lg': '1024px',
            '-xl': { max: '1279px' },
             'xl': '1280px',
        },
        container: {
            center: true,
            padding: '18px',
        },
        maxWidth: {
            0: 'none',
            xs: '425px',
            sm: '600px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            24: '24em',
            26: '26em',
            28: '28em',
            30: '30em',
            32: '32em',
            34: '34em',
            36: '36em',
            38: '38em',
            40: '40em',
            42: '42em',
            44: '44em',
            46: '46em',
            48: '48em',
        },
        spacing: {
              0:  '0',
              1:  '1px',
              2:  '2px',
              3:  '3px',
              4:  '4px',
              5:  '5px',
              6:  '6px',
              9:  '9px',
             12: '12px',
             15: '15px',
             18: '18px',
             21: '21px',
             24: '24px',
             27: '27px',
             30: '30px',
             36: '36px',
             42: '42px',
             48: '48px',
             54: '54px',
             60: '60px',
             72: '72px',
             84: '84px',
             96: '96px',
            108: '108px',
            120: '120px',
        },
       fontSize: {
             9:  '9px',
            10: '10px',
            11: '11px',
            12: '12px',
            13: '13px',
            14: '14px',
            15: '15px',
            16: '16px',
            17: '17px',
            18: '18px',
            19: '19px',
            20: '20px',
            22: '22px',
            24: '24px',
            28: '28px',
            30: '30px',
            34: '34px',
            36: '36px',
            40: '40px',
            42: '42px',
            48: '48px',
            54: '54px',
            60: '60px',
            72: '72px',
            96: '96px',
        },
    },
    variants: {},
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
