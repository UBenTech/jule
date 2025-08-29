/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/**/*.php",
    "./includes/**/*.php",
    "./admin/**/*.php",
    "./templates/**/*.php"
  ],
  theme: {
    extend: {
      // You can extend the default theme here with custom colors, fonts, etc.
      // For example:
      // colors: {
      //   'brand-blue': '#1992d4',
      // },
    },
  },
  plugins: [
    // You can add official Tailwind plugins here
    // require('@tailwindcss/forms'),
  ],
}
