const pluginSEO = require("eleventy-plugin-seo");
//const { DateTime } = require("luxon");
const markdownIt = require("markdown-it");
const markdown_options = {
	html: true,
	linkify: true,
	typographer: true,
	breaks: false
};
const anchor = require('markdown-it-anchor');
const svgContents = require("eleventy-plugin-svg-contents");
const CleanCSS = require("clean-css");
const { EleventyEdgePlugin } = require("@11ty/eleventy");
//const eleventyPluginFilesMinifier = require("@sherby/eleventy-plugin-files-minifier");

module.exports = config => {

	// Cloudinary
	config.cloudinaryCloudName = "aarongustafson";
	config.hostname = "https://tipr.mobi";

	// Markdown
	let md = markdownIt(markdown_options)
						 .use( anchor, {
								level: 2,
								permalink: anchor.permalink.ariaHidden({
									placement: 'before'
								})
							})
						 .use(require("markdown-it-attrs"))
						 .use(require('markdown-it-footnote'));
	md.renderer.rules.footnote_caption = (tokens, idx/*, options, env, slf*/) => {
		var n = Number(tokens[idx].meta.id + 1).toString();

		if (tokens[idx].meta.subId > 0) {
			n += ':' + tokens[idx].meta.subId;
		}

		return n;
	};
	config.setLibrary( "md", md );

	// Layout aliases
	config.addLayoutAlias("base", "layouts/base.html");
	config.addLayoutAlias("page", "layouts/page.html");
	
	// Passthru
	config.addPassthroughCopy({ "src/static": "/" });

	// Upgrade Helper
	//const UpgradeHelper = require("@11ty/eleventy-upgrade-help");
	//config.addPlugin(UpgradeHelper);
	
	// Plugins
	config.addPlugin(EleventyEdgePlugin);
	config.addPlugin(pluginSEO, require("./src/_data/seo.json"));
	config.addPlugin(svgContents);
	//config.addPlugin(eleventyPluginFilesMinifier);
	
	// Filters
	config.addFilter("cssmin", function(code) {
    return new CleanCSS({}).minify(code).styles;
  });
	config.addFilter("markdownify", text => {
		return md.renderInline( text );
	});
	const filters = require('./_11ty/filters');
	Object.keys(filters).forEach(filterName => {
		config.addFilter(filterName, filters[filterName]);
	});
	
	// Collections
	config.addCollection("sitemap", function(collectionApi) {
		// get unsorted items
		return collectionApi
						.getAll()
						.filter( item => ( "sitemap" in item.data && item.data.sitemap === true ) )
						.sort( (a,b) => {
							a = a.url.substring( 1 );
							b = b.url.substring( 1 );
							return a < b ? -1 : a > b ? 1 : 0;
						});
	});

	// Front Matter
	config.setFrontMatterParsingOptions({
		excerpt: false,
	});

	// Nunjucks
	config.setNunjucksEnvironmentOptions({
		//throwOnUndefined: true,
		trimBlocks: true,
		lstripBlocks: true
	});

	// Config
	return {
		markdownTemplateEngine: "njk",
		dataTemplateEngine: "njk",
		htmlTemplateEngine: "njk",
		dir: {
			input: "src",
			output: "_site"
		}
	};
};