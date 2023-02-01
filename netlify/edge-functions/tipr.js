import { process } from "./../../_site/j/process.js";

function setCookie(context, name, value) {
	context.cookies.set({
		name,
		value,
		path: "/",
		httpOnly: true,
		secure: true,
		sameSite: "Lax",
	});
}

export default async (request, context) => {
	let url = new URL(request.url);

	// Save to cookie, redirect back to form
	if (url.pathname === "/process/" && request.method === "POST")
	{
		if ( request.headers.get("content-type") === "application/x-www-form-urlencoded" )
		{
			let body = await request.clone().formData();
			let postData = Object.fromEntries(body);

			let result = process( postData.check, postData.percent );

			setCookie( context, "check", result.check );
			setCookie( context, "tip", result.tip );
			setCookie( context, "total", result.total );

			return new Response(null, {
				status: 302,
				headers: {
					location: "/results/?" + JSON.stringify(result) + JSON.stringify(postData),
				}
			});
		}
	}

	return context.next();
};