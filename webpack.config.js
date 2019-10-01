const path = require('path');

module.exports = {
  // optimization:{
  //   minimize: false,
  // },
	entry: './assets/js/cg-cookie-consent.js',
	mode: 'development',
	output: {
		filename: 'main.js',
		path: path.resolve(__dirname, 'dist')
	},
	module: {
		rules: [{
			test: /\.js$/,
			exclude: /node_modules/,
			use: {
				loader: 'babel-loader',
			}
		}]
	},
};