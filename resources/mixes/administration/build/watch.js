require('./check-versions')();

process.env.NODE_ENV = 'production';

var buildWebpackConfig = require('./webpack.prod.conf');
var chalk = require('chalk');
var config = require('../config');
var path = require('path');
var rm = require('rimraf');
var merge = require('webpack-merge');
var shell = require('shelljs');
var webpack = require('webpack');
var webpackConfig = merge(buildWebpackConfig, {
    plugins: [
        new webpack.ProgressPlugin()
    ],
    watch: true
});

rm(path.join(config.build.assetsRoot, config.build.assetsSubDirectory), err => {
    if (err) throw err;
    webpack(webpackConfig, function (err, stats) {
        if (err) throw err;
        console.log('\n');
        process.stdout.write(stats.toString({
                colors: true,
                modules: true,
                children: false,
                chunks: false,
                chunkModules: false
            }) + '\n');
        var assetsPath = path.join(__dirname, '../../../../../../statics/assets/admin');
        var monacoPath = path.join(__dirname, '../../../../../../statics/assets/monaco');

        console.log(chalk.cyan('  Moving Dist files to path ' + assetsPath + '\n'));

        shell.rm('-rf', assetsPath);
        shell.mkdir('-p', assetsPath);
        shell.rm('-rf', monacoPath);
        shell.mkdir('-p', monacoPath);
        shell.config.silent = true;
        shell.cp('-R', path.join(__dirname, '../dist/assets/admin/css'), assetsPath);
        shell.cp('-R', path.join(__dirname, '../dist/assets/admin/fonts'), assetsPath);
        shell.cp('-R', path.join(__dirname, '../dist/assets/admin/images'), assetsPath);
        shell.cp('-R', path.join(__dirname, '../dist/assets/admin/js'), assetsPath);

        console.log(chalk.cyan('  Moving Monaco files to path ' + monacoPath + '\n'));
        shell.cp('-R', path.join(__dirname, '../node_modules/monaco-editor/min/vs'), monacoPath);
        shell.config.silent = false;

        console.log(chalk.cyan(`  Build completed at ${(new Date()).toLocaleString()}.`));
        console.log(chalk.cyan('  Watching ...\n'));
    });
});