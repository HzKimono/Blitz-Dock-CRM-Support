module.exports = [
  {
    ignores: ['assets/js/**/*.min.js']
  },
  {
    files: ['assets/js/**/*.js'],
    languageOptions: {
      ecmaVersion: 2019,
      sourceType: 'script',
      globals: {
        console: 'readonly',
        document: 'readonly',
        Element: 'readonly',
        Node: 'readonly',
        URL: 'readonly',
        window: 'readonly',
        HTMLInputElement: 'readonly'
      }
    },
    rules: {
      'no-unused-vars': ['error', { vars: 'all', args: 'after-used', ignoreRestSiblings: true, argsIgnorePattern: '^_', caughtErrors: 'all', caughtErrorsIgnorePattern: '^_' }],
      'no-undef': 'error',
      eqeqeq: ['error', 'smart']
    }
  }
];