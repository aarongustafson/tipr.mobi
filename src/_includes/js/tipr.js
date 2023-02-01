function toDollars( value )
{
	value = parseFloat( value, 10 );
	return parseFloat( twoDecimals( value ), 10 );
}
function twoDecimals( value )
{
	return ( Math.round( value * 100 ) / 100 ).toFixed(2);
}

var process = function( check, tip_percentage ){

	check = check + "";
	check = toDollars( check.replace(",","") );
	tip_percentage = tip_percentage + "";
	if ( tip_percentage.indexOf("%") > -1 )
	{
		tip_percentage = parseInt( tip_percentage.replace("%",""), 10 ) / 100;
	}
	else
	{
		tip_percentage = parseFloat( tip_percentage, 10 );
	}
	
	var original_tip = toDollars( Math.round( check * tip_percentage * 100 ) / 100 );
	//console.log( check, tip_percentage, original_tip );

	// test and new tip amounts
	var new_tip = 0;
	var test_tip = original_tip;

	// total placeholder
	var total;
	
	// rounding info (making sure we're not way undertipping)
	while ( new_tip < original_tip )
	{
		// find the base total
		var base = twoDecimals( check + test_tip );
		//console.log( "base", base );

		// figure out the # of digits to mirror
		var digits = Math.floor( base.replace(".","").length / 2 );
		//console.log( "digits", digits );

		// create the palindrome
		var arr = base.split("");
		var i, j;
		//console.log( "arr", arr );
		for ( i = arr.length-1, j=0; j < digits; i--, j++ )
		{
			if ( arr[i] == "." )
			{
				j--;
			}
			else
			{
				arr[i] = arr[j];
			}
			//console.log( "arr", arr );
		}
		
		// build the total
		total = toDollars( arr.join("") );
		//console.log( "total", total );

		// get the tip amount
		new_tip = toDollars( total - check );
		//console.log( "new_tip", new_tip );
		
		// if it's less, add a dollar
		if ( new_tip < test_tip )
		{
			test_tip += 1;
		}
	}
	
	return {
		check: twoDecimals( check ),
		tip: twoDecimals( new_tip ),
		total: twoDecimals( total )
	};
};