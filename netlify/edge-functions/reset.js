export default async (request, context) => {
  let url = new URL(request.url);

  // Link to ?reset will delete the cookie
  if(url.searchParams.get("reset") === "") {
    // Awkward part here is that delete needs to happen on /
    // (can’t happen on /critical-css/) due to cookie path on root
    context.cookies.delete("check");
    context.cookies.delete("tip");
    context.cookies.delete("total");

    return new Response(null, {
      status: 302,
      headers: {
        location: "/",
      }
    });
  }

  return context.next();
};