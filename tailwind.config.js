// tailwind.config.js
const plugin = require('tailwindcss/plugin')

module.exports = {
    important: '.tailwind-scope',
    content: ["./**/*.php"],
    corePlugins: {
        fontFamily: false,
        preflight: false, // we scopen preflight zelf
    },
    theme: { extend: {} },

    plugins: [
        plugin(function({ addBase }) {
            addBase({

                /* === FULL PRE-FLIGHT RESET GESCOPED === */
                '.tailwind-scope *, .tailwind-scope ::before, .tailwind-scope ::after': {
                    boxSizing: 'border-box',
                    borderWidth: '0',
                    borderStyle: 'solid',
                    borderColor: 'currentColor',
                },

                '.tailwind-scope html': {
                    lineHeight: '1.5',
                    WebkitTextSizeAdjust: '100%',
                    MozTabSize: '4',
                    tabSize: '4',
                    fontFamily: 'sans-serif',
                },

                '.tailwind-scope body': {
                    margin: '0',
                    lineHeight: 'inherit',
                },

                /* HEADINGS reset */
                '.tailwind-scope h1, .tailwind-scope h2, .tailwind-scope h3, .tailwind-scope h4, .tailwind-scope h5, .tailwind-scope h6': {
                    fontSize: 'inherit',
                    fontWeight: 'inherit',
                },

                '.tailwind-scope a': {
                    color: 'inherit',
                    textDecoration: 'inherit',
                },

                '.tailwind-scope button, .tailwind-scope input, .tailwind-scope optgroup, .tailwind-scope select, .tailwind-scope textarea': {
                    fontFamily: 'inherit',
                    fontSize: '100%',
                    lineHeight: 'inherit',
                    color: 'inherit',
                    margin: '0',
                    padding: '0',
                },

                '.tailwind-scope button, .tailwind-scope select': {
                    textTransform: 'none',
                },

                '.tailwind-scope button, .tailwind-scope [type="button"], .tailwind-scope [type="reset"], .tailwind-scope [type="submit"]': {
                    appearance: 'button',
                    backgroundColor: 'transparent',
                    backgroundImage: 'none',
                },

                /* Images */
                '.tailwind-scope img, .tailwind-scope video': {
                    maxWidth: '100%',
                    height: 'auto',
                },

                /* Root interactions */
                '.tailwind-scope [hidden]': {
                    display: 'none !important',
                },
            })
        })
    ],
}
