const js = require('@eslint/js');

module.exports = [
  js.configs.recommended,
  {
    files: ['assets/js/**/*.js'],
    languageOptions: {
      ecmaVersion: 5,
      sourceType: 'script',
      globals: {
        console: 'readonly',
        document: 'readonly',
        Element: 'readonly',
        Node: 'readonly',
        window: 'readonly'
      }
    }
  }
];