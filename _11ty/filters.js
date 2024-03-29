const { DateTime } = require("luxon");
const widont = require("widont");

function parse_date( date ){
	if ( ! date ) {
		return DateTime.now();
	}
	// try JS
	var the_date = DateTime.fromJSDate(date);
	// then try ISO
	if ( the_date.invalid ) {
		the_date = DateTime.fromISO(date);
	}
	// fallback to SQL
	if ( the_date.invalid ) {
		the_date = DateTime.fromSQL(date);
	}
	return the_date;
}

module.exports = {
	
	readable_date: date => {
		return parse_date( date ).toFormat("dd LLL yyyy");
	},
	ymd_date: date => {
		return parse_date( date ).toISODate();
	},
	machine_date: date => {
		return parse_date( date ).toISO();
	},

	strip_links: text => {
		return text.replace(/<\/?a[^>]*>/gi, "");
	},

	trim_newlines: text => {
		return text.replace(/[\r\n]+/g, "");
	},

	widont: text => {
		return `${widont( text )}`;
	},

	limit: (array, limit) => {
		return array.slice(0, limit);
	},

	unescape: html => {
		html = html || "";
		return html.replace(/&gt;/g, ">")
						.replace(/&lt;/g, "<")
						.replace(/&quot;/g, '"');
	},

	minus: ( a, b ) => parseInt(a,10) - parseInt(b,10),
	size: array => !array ? 0 : array.length,
	required: ( items, requirements ) => {
		var type;
		if ( requirements.indexOf( "||" ) > 0 )
		{
			type = "or";
			requirements = requirements.split( "||" );
		}
		else if ( requirements.indexOf( "&&" ) > 0 )
		{
			type = "and";
			requirements = requirements.split( "&&" );
		}
		else
		{
			type = "single";
			requirements = [ requirements ];
		}
		requirements = requirements.map(item => item.trim());
		return items.filter(item => {
			let i = requirements.length;
			// all
			if ( type == "and" )
			{
				while ( i-- )
				{
					if ( ! item[requirements[i]] )
					{ 
						return false;
					}
				}
				return true;
			}
			// any
			else
			{
				while ( i-- )
				{
					if ( item[requirements[i]] )
					{ 
						return true;
					}
				}
				return false;
			}
		});
	},

	content_type: path => {
		let type = "post";
		if ( path && path.indexOf("/links/") > -1 )
		{
			type = "link";
		}
		return type;
	},
	path_in_scope: ( path, scope ) => {
		return path.indexOf( scope ) > -1;
	},

	json: ( obj ) => {
		return JSON.stringify(obj, null, 2);
	}
};