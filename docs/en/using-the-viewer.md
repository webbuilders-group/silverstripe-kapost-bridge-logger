Using the Viewer
=================
With the module installed all requests to and responses from the Kapost Service endpoint will be logged in the database by default for a period of 30 days. To access the viewer login to the SilverStripe CMS and go to the Kapost Content section then click on the "Kapost Bridge Log" tab.

Logs are listed in reverse chronological order, in other words the most recent request is at the top and the oldest is at the bottom. Each request is listed by its method name (i.e. metaWeblog.getPost) and its date and time of the request. To view a single request click on the item in the Logs list. The right hand panel will now show details about the request including a link to the destination object if available<sup>1</sup>. The main raw request and response xml is available for viewing in accordions<sup>2</sup>. If you neeed to copy either the response or request simply double click on the xml and you will be able to copy the xml easily.



--------
1. For the destination object link to appear the destination must either be a page or one handled through the ``KapostBridgeLog.updateObjectLookup`` extension point. See [the readme](../../README.md#extension-points) for information on how to do this.
2. Some requests if they error out may not be logged at all or may have a partial log. If you are getting an error in Kapost you should [enable error logging](https://docs.silverstripe.org/en/developer_guides/debugging/error_handling/) for your site to assist you in debugging.
