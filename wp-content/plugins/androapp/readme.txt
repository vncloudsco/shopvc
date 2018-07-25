=== Androapp - Native Android & IOS mobile app for wordpress site ===

Contributors: Genius Fools
Tags: Android app, Mobile App, Native Mobile App, app for WordPress, mobile app plugin, native app plugin, push notifications, website to mobile app, WordPress app builder, WordPress app maker, WordPress blog app, androapp, deep linking, ios app
Requires at least: 3.3
Tested up to: 4.8.1
Stable tag: 16.00
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Native mobile app for android / ios platform, create a beautiful mobile app for your wordpress blog in minutes, no programming knowledge required.

== Description ==
Native mobile app for android / ios platform, create a beautiful mobile app for your wordpress blog in minutes, no programming knowledge required.

<blockquote>
Test your app on your phone in minutes, It will really take minutes, Try it !!
</blockquote>
= Features =
1. Unlimited Push Notifications
1. Monetize your app using Admob and Appnext ad units
1. Deep linking Support
1. Inbuilt Sharing channels with image share - Facebook, WhatsApp and more through default android sharing intents.
1. Wordpress/Facebook comments support
1. Offline Save
1. Image Zoom, Share and Save
1. Parallax Effect for Featured image on post detail page
1. Caching - option to use WP Super cache to cache mobile app requests at server side.
1. Infinite Scroll
1. Multiple themes to suit your website
1. Dynamic Settings for Menu, Image Rendering, Preview Text, Share Text, Post Page settings, Ads placements, Notification Style, Theme Colors and more.
1. Customize App Colors as per your blog or company website.
1. Your own company Logo
1. No reference to AndroApp, it is a complete white label app

= Demo Mobile Apps =
Here are few of the mobile apps built using our plugin

1. <a target="_blank" href="https://play.google.com/store/apps/details?id=mobi.androapp.autostrada.c3402&hl=en">Autostrada Android App</a> for <a target="_blank" href="https://autostrada.tv/">autostrada.tv</a>
1. <a target="_blank" href="https://play.google.com/store/apps/details?id=mobi.androapp.storytal.c2123">Hindi Stories Android App</a> for <a target="_blank" href="http://hindi.storytal.com/">hindi.storytal.com</a>
1. <a target="_blank" href="https://itunes.apple.com/app/id1229480101">Hindi Stories IOS App</a> for <a target="_blank" href="http://hindi.storytal.com/">hindi.storytal.com</a>

= Android App Updates =
See what we have been doing on the android app side, see <a href="https://androapp.mobi/blog/category/updates" target="_blank">Android App Updates</a>

= Pricing =
Your mobile app from AndroApp is completely **free for the first 1 month**, no credit card needed.

Annual Renewal:

Android Only:  $66/year <a href="https://www.payumoney.com/store/product/4a48ec6c814b2f0a8f0e87d426ece891">pay here</a>
IOS Only: $66/year <a href="https://www.payumoney.com/store/product/12df691bdc0d036cfe842c34b1df5344">pay here</a>
IOS & Android both: $110/year (discount of $22) <a href="https://www.payumoney.com/store/product/23461b5129b895e3b4e226a48ab3cd83">pay here</a>

**In case you are not willing or not able to pay, your app will continue to work without any ads from you and instead we reserve to show few of our ads**

For any questions, do get in touch with us @ contact@androapp.mobi.

In case you are not able to pay via payumoney, and want to pay via paypal, please drop us an email at contact@androapp.mobi

Visit our site for more information - http://androapp.mobi/

Follow us on <a href="https://plus.google.com/+AndroappMobiPlugin" target="_blank" >Google+</a>, <a href="https://www.facebook.com/androapp.mobi" target="_blank" >Facebook</a>

**AndroApp does not support WooCommerce and BuddyPress plugins**
== Installation ==
Follow these instructions for getting your android app apk link
1. Upload the `androapp` folder to the `/wp-content/plugins/` directory or use plugin search - Admin > Plugins > Add new > Search for 'AndroApp'
1. Activate the Plug-in
1. Follow 5 simple steps in Settings->AndroApp configuration page.
1. You will receive apk link for your android app in your mailbox, open the link from your mobile, install the apk and test.(Don't worry about the warning, as it is a standard warning for installing any direct apk).

For detailed instructions http://androapp.mobi/#get-started
== Frequently Asked Questions ==
= How to get My Mobile App = 
Just follow 5 simple steps in Settings->AndroApp configuration page, we will send android app apk link to your e-mail. Click on the link from your mobile and your mobile app will be installed on your phone. test out your new android app for your wordpress site and publish on google play store.

= No Post Feeds are visible in the Mobile App = 
Few clients have faced the issue due to bad timezone settings, try changing your timezone settings and see that there should not be any exception in httpd logs.

= Can i monetize my mobile application? =
Yes, we have integrated admob sdk, you just need to fill ad unit id's in account settings page, you have full control over the ad slots and frequency.
We also support AppNext and other ad networks via mobsdk, try them out for your mobile app, let us know if you have any issues in using them.

= Can i change My Mobile Application Logo = 
Yes, you can use your own logo, just go to Look & Feel Section and upload a 512x512 size png image.

= I don't like default theme, are there more theme selection options? =
Yes, You can change your mobile app theme at runtime(no need to create new build) from AndroApp Settings > Look & Feel Section.

= Can i change theme colors =
Yes, We give the option to customize theme colors, you can change it to match your blog or company website.

= Do you support Push notifications =
Yes, we by default send push notification everytime you publish a new post. You need to signup for Firebase Cloud Messaging API's, See instructions <a href="https://androapp.mobi/blog/setup-firebase-cloud-messaging/182">here</a> and fill in Google API key and Project number in Account Settings Page.

= Will it send push notification for all the posts? =
By Default yes, but you can choose not to send push notification by selecting "Do not send Push Notification" checkbox on edit post page.

= Do you provide IOS app for apple store as well? =
 We do provide IOS apps now in Beta mode, For more info contact is @ contact@androapp.mobi.

= Do you support UTF-8? Few of the posts have unicode characters, will they display fine? =
We do support utf-8 and they will display perfectly fine.

= Do you support multiple languages? = 
We can provide you the app with all the text in your language, right now it is not automated, so you have to provide us the texts you want to use.

= How to pay for the next year subscription fee ? =
You can purchase annual subscription from <a href="https://www.payumoney.com/store/product/4a48ec6c814b2f0a8f0e87d426ece891" target="_blank">here</a>. Mention your email id and site link in shipping address. We will activate your annual subscription. In case you are not able to pay via payumoney, and want to pay via paypal, please drop us an email at contact@androapp.mobi

= Push notification not working for my site, what to do ? =
First ensure that you fill the correct project number and api key in account settings tab, create a new build, install it in your mobile. Push notification will be send after around 20-30 minutes. Also enable curl on your server(if not enabled already).

= Does AndroApp Support WooCommerce? =
We used to support woocommerce earlier, but we stopped supporting it to continue focus on mainstream apps.

= Does AndroApp Support BuddyPress? =
No, we do not support buddypress plugin

= Google play upload fail with following error = 
You uploaded an APK that is signed with a key that is also used to sign APKs that are delivered to users. Because you are enrolled in App Signing, you should sign your APK with a new key before you upload it.
Don't enable App Signing - <a href="https://androapp.mobi/blog/google-play-upload-failed-due-sign-issue/418">follow this</a>

= Can i deactivate WP REST API plugin? =
WP Rest api comes embedded in wordpress now, we support it in Android and IOS app both. Android app is backward compatible, disabling plugin should not effect your existing android app.
However IOS App is not backward compatible, you need to publish a new app after you disable the plugin (old app will not work).
Also, when you activate/deactivate WP Rest API plugin, you might need to do this to clear some caches
go to Settings->Permalink and Save (without making any changes).

== Screenshots ==
1. homepage screen with two different themes
1. search posts and share options
1. Facebook and Wordpress Comments support
1. Supports banner ads and high CPM Interstitial Ads
1. Save for later (offline save option)
1. Parallax effect for featured image and option to save/zoom any image
1. 5 Steps to your mobile app guide at Settings->AndroApp configuration page.
1. Configure app settings dynamically, like menu, image rendering style, share text, post page settings etc.
1. Push notification, facebook share and monetization settings.
1. Customize theme and Generate build with a click of a button.
1. App Menu with FontAwesome Icons support


== Changelog ==
= 16.00 =
Fixed Android app crash issue: Transaction too large, create new apk and publish on play store after testing
= 15.04 =
custom post types/taxonomies support and few other fixes for IOS app, no need to create new apk for android, drop us an email for new IOS build.
= 15.03 =
Removed status bar while video is played in full screen mode
= 15.02 =
Changes in installation steps, New apk creation is not needed
= 15.01 =
1. Fixed not able to disable comments issue (in Android)
= 15.00 =
1. Removed google analytics tracking by androapp for crash and number of hits
1. Updated Appnext and Admob library versions
1. Sending post id instead of titles (to be more GDPR compliant)
1. Read more about AndroApp & GDPR here https://androapp.mobi/blog/androapp-and-gdpr/493
= 14.01 =
1. Fixed push notifications and share issues for Android Oreo
1. Added configuration for stacking push notification on Android
= 13.04 =
1. disabling auto push notifications for custom post types (for now)
= 13.03 =
1. Fixed push notification not triggering on new post publish, this issue started in 13.01 plugin update, just the plugin update is enough, no need to create a new apk
= 13.02 =
1. added support for WPBakery Visual Composer plugins tags
1. Fixed wrong author issue (not reproducible on all websites)
1. added apostrophe support in category/tag base
1. Fixed possible crashes
= 13.00 =
1. Added Custom Post Type & Custom Taxonomy Support
1. Few possible crash issue fixes in android app
= 12.00 =
1. updated target sdk version for android app to 26
1. added runtime permissions in android app
1. Fixed NEW_POST internalization text in push notification
1. Showing push notification queue for debugging
1. few other backend improvements
= 11.04 =
1. Fixed an installation issue, no need to create new apk for this release.
= 11.03 =
1. Fixed post details reload issue for lower versions (< MarshMallow)
1. Fixed Don't send push notification flag issues
1. Fixed crash issue on clicking back while a link is loading
1. Added Cairo Bold font
= 11.02 =
1. Using bookmark icon instead of Save Offline text behind 3 dots option menu
1. Option to control where to open an url: In App or Mobile Browser
1. Faster loading of links - earlier it used to wait without any loading icon - now opening the post detail first with loading icon
1. Fixed: Pull down refresh adding posts back on category/tags screen
= 11.00 =
1. Added Top Slider Menu, configure menu from AndroApp->Configure tab, create new apk, test and publish to play store.
= 10.05 =
1. Fixed a php version compatibility issue for plugin, no need to publish new apk for this update
= 10.04 = 
1. Added pull down refresh for android, you need to generate new build and publish
= 10.02 =
1. Added support for File Upload
1. Fixed image not showing when using scheme relative url (specially scene with jetpack photon)
1. create new apk if facing any of the above 2 issues, please test before you publish to play store.
= 10.01 =
1. Fixed issue in setting post content type as load url (introduced in 10.00) - only plugin update fixes it, no need to publish new apk (if you are already on 10.00)
= 10.00 =
1. Added menu icons support, need to publish new build
= 9.02 =
1. Fixed a backend issue to show push notification stats
1. Fixed an issue with push notifications when bulk send is disabled, by default it is enabled, so either you switch to bulk send or release a new apk.
= 9.01 =
fixed one bug in the last release, you need to publish new apk for using loadimages content type, else just update the plugin
= 9.00 =
1. Added new post content type i.e. SlideShow - to open all images in the post as slideshow
= 8.04 =
1. Minor backend enahancements, no need to create new apk
= 8.02 =
1. fixed a minor issue to support older version of php
1. few other backend improvements in the server side.
1. Added option to save firebase app id for IOS
1. Fixed status bar color issue
= 8.00 =
1. Added support for AppNext Banner Ads
1. More control to ads, you can decide to show particular ads to show only on selected screens
1. Moving comments icon from menu to right bottom as floating button which hides/shows on screen movement.
1. Added Facebook comments support
1. listview ad to work as middle ad on post screen
1. few fixes for woocommerce app
1. Few more bug fixes
= 7.04 =
1. Added ability to override post content type in AndroApp->Configure page
1. Added backend support for IOS app and in-built WP rest api
= 7.03 =
1. Added image zoom, save and share option
1. Fixed bottom ad not shown on post detail page issue
1. Fixed focus change issue while swiping left/right
1. changed Position of Saved Posts menu option, will be shown on top OR just after Home menu item(if there)
= 7.02 =
1. Added Offline Save Option.
1. Added flag for notification cache and few other improvements in notifications.
1. Added flag for prallax effect(removed featured image show/hide flag, you can now control it using parallax flag and css).
1. Fixed: Not easy to scroll while typing the long comment.
1. Fixed: Comment textbox not showing fully.
1. Fixed: Comments screen invisible after clicking on add comment textbox.
1. Fixed: Removed View Comment link from the comments from Wordpress Comment SEO plugin.
= 6.09 =
1. New Parallax effect for featured image in post detail page, check out this https://androapp.mobi/blog/androapp_parallax_effect
1. Better Quick Return pattern
1. Using better image library for fast image loading
1. Fixed: Infinite left-right swipe issue, due to some issue, it got limited to 10 posts.
1. Fixed: issue in clicking on category link in list view pages, added its area so that clicking on it is easier now.
1. Fixed: app crash on playing video on full screen.
= 6.08 =
1. Few hygiene updates as suggested by wordpress team.
= 6.07 =
1. Added support for nested menus
1. Handling mailto and tel links properly
1. Fixed: posted comments are showing awaiting moderation even if they are auto-approved
1. Fixed: app crashes on clicking on gear icon on comments page
= 6.06 =
1. Fixed a bug for bulk push notification send
= 6.05 =
1. Added Bulk Send functionality for push notifications, improved performance, reduces server load, need to upgrade to firebase cloud messaging service first.
1. Showing category link instead of author in News theme.
= 6.04 =
1. Updated to Firebase Cloud messaging api for push notification, please migrate your apps and add google app id in account settings tab.
1. Better looking menu, will add more features like expandable menu options soon.
1. Fixed one push notification going even if Do not send push notification checkbox was checked issue for scheduled posts.
1. Removing blank space while removing google adsense ads.
= 6.03 =
1. Option to add app only html code/teax on post page
1. Fixed: Push notification redirecting to homepage when homepage type is set to single post/page
= 6.01 =
1. Fixed app not working on Ice CreamSandwich and Jelly Bean versions(< 5.0) issue
1. Few more minor changes for woocommerce sites
= 6.00 = 
1. Added Deep Linking support
1. Added option to update product quantity for woocommerce
= 5.11 =
1. Added mopub ad provider support, you can use multiple ad networks through this.
1. removed restriction from our side on using top/bottom/listview ads, you should use it wisely yourself so that google does not ban your app.
= 5.09 =
1. Moved AndroApp option from Settings to Main Menu
1. Improved push notification performance, reduced number of DB queries
1. Added SelfPush option, you can now trigger a notification for a post id anytime you wish.
1. Added push notification statistics.
= 5.08 =
1. Added ability to show post from a particular category,tag,author,product_tag OR product_cat
1. Fixed one rendering issue on listing page, when there is only 1 or 2 posts to show
= 5.07 =
1. Added fix for yuzo related post shortcode (when post content type is postprocessed)
1. Updated logic to read excerpt from Yoast SEO plugin
= 5.06 =
1. Added font support, you can select from few google fonts
1. utf-8 bom character issue fix(was reproducible on few sites only)
1. some crash fixes
= 5.05 =
1. Fixed crash issue on opening external link (update required only if you are using 5.0.4 version, last release)
= 5.04 =
1. Minor improvements in Quick return
1. Fixed internal link issue opening in webview for the websites having the pattern www.xyz.com/abc, where abc can be some text like blog
= 5.03 =
1. Added Quick Return pattern, it hides/shows action bar based on user behavior, giving more device space for the content.
= 5.02 =
1. Added option to remove Google adsense units from post content (in Account Settings tab)
1. Moving androapp to https, making your connection to androapp more secure
= 5.01 =
1. Added wordpress audio/video support (no need of publishing new apk)
= 5.00 =
1. Fixed internal link not opening in webview issue
1. handling affiliate product buy button (for woocommerce)
1. Fixed post title showing junk characters in push notification issue
= 4.09 =
1. Added splash screen, you can set your own image, by default application icon will be shown
1. Added RTL Support for languages like persian, arabian urdu etc. App will automatically convert to RTL mode for languages which need RTL. menu position etc will change to right side.
1. Better Resolution for Push Notification image for news theme
1. fixed display name issue
1. Updated push notification registration, using latest GcmListener instead of broadcast receiver from google.
= 4.08 =
1. Fixed Admob interstitial ad not showing issue, until appnext placement id is put
1. Corner fix for woocommerce sites when no shippable countries are present
= 4.07 =
1. Added product tag links support for woocommerce
= 4.06 =
1. Added search, it might not work properly if you are using any search plugin, please check and disabled in on Configure tab.
= 4.05 =
1. Added AppNext Ads Support
1. Ability to show interstitial ads on page swipes
1. Option to change top and bottom ad unit types
1. Fixed cart icon visible on Comments Settings Screen
1. Reduced free period to 1 month for new users
1. Few more fixes
= 4.04 =
1. option to show a post or page on homepage
1. Showing Vendor info for woocommerce app with WC MArketplace plugin.
= 4.03 =
1. Fixed comments issue(caused in last build), all the users who are using app version 4.0.0 and having comments option enabled, please update the app.
1. option to show list of pages on the homepage instead of posts.
1. few minor fixes.
= 4.02 =
1. woocommerce beta
1. default settings option for do not send push notifications
1. Sticky top/bottom ads on post page
1. not supporting api versions less than 11 anymore, i.e. supporting Honeycomb or later
1. Check your renewal date on Get Started tab of AndroApp settings page

= 4.01 =
1. Year free extension ends today, from now on new users will get only first 3 months free usage.
2. Price is set to increase to $60/year(20% rise over a year) soon, you can pay early to save $10, here is the <a href="https://www.payumoney.com/store/product/4a48ec6c814b2f0a8f0e87d426ece891" target="_blank">payment link</a>

= 4.00 =
1. Loading icon on homepage screen, it gives the correct error message for the new user
1. Gmail like Swipe Left Right feature on post pages
1. Better looking share, menu and comments icons
1. Animations on every transition
1. Option to set status bar color
1. Task Description color same as app action bar background color
1. Some background changes to make the app faster, it keeps less data in memory and releases unused resources while moving in-out from one screen to another

= 3.25 = 
Added curl check for push notification, no need to new build
= 3.24 = 
Added Restore to default settings options in the plugin, no change in the app
= 3.22 = 
Fixed issue in creating builds for new users. Existing users no change in app or functionality
= 3.20 =
Small fix for creating the build for new users, no change in the app
= 3.18 = 
Fixed an issue where new users was seeing a blank screen on first time, app was working fine for second start. So if you are on app version 2.0.7(you can see your app version in your google play store account), please publish a new build with version 2.0.8 or more.
= 3.17 =
1. Added option to change texts used in the app, you can change the text on the fly
1. Tracking outbound links
1. Added few more ad size options
= 3.13 =
Fixed firefox issue in generating build, no need to publish a new apk
= 3.12 = 
1. Added Google Analytics support
1. fixed multiple sound issue on receiving push notification
= 3.1 =
1. Added wordpress comments support, need to create a new build
1. sending external links to browser
= 3.02 =
1. Handling pages, posts links in menu options correctly.
1. Removed push notification type settings. in the interest of end customer, two continuous notifications will be shown separately and remaining more will be added to the stack automatically, sound and vibrate is also more controlled now.
1. Fixed issue: interstitial ad not shown sometimes
= 3.01 =
1. pre-fetching home page data on stack push notification
1. controlled sound notification, now ringing only twice in a row
1. Removed dedicated facebook and whatsapp share icons as it makes UI more cleaner, share icon is directly visible now.
1. Reduced apk size from 3.3MB to 2.0MB, a 40% reduction in size.

= 3.0.0 = 
1. Added new **News** theme.
1. enhanced round corners with shadow boxes in default theme.
1. Now you can change theme colors at the runtime, you can change your app colors anytime.
1. Using thumbnails for featured images, to enable faster loading.
1. Added post title, author, time ago, category in post detail page.
1. Note:- you have to create a new build, test it and then upload to google play to have all these features.

= 2.0.4 =
1. Showing featured image on top on post page
1. Fixed blank screen issue(was added by mistake during video enabled release)
1. Fixed Html not loading properly on some devices issue(This was also due to video changes)
1. (upgrade must if your app version is in 1.0.6)
= 2.0.3 =
1. Added YouTube Support
1. Using featured image if available and than trying for image from post content
= 2.0.1 =
Added How to fix the DateTimeZone error in WP REST API plugin

== Upgrade Notice ==
= 16.00 =
Fixed Android app crash issue: Transaction too large, create new apk and publish on play store after testing
= 15.04 =
custom post types/taxonomies support and few other fixes for IOS app, no need to create new apk for android, drop us an email for new IOS build.
= 15.03 =
Removed status bar while video is played in full screen mode
= 15.02 =
Changes in installation steps, New apk creation is not needed
= 15.01 =
Fixed not able to disable comments issue (in Android)
= 15.00 =
Updated Appnext & admob SDK versions, removed androapp analytics, read more about AndroApp & GDPR https://androapp.mobi/blog/androapp-and-gdpr/493
= 14.01 =
Fixed push notifications and share issues for Android Oreo, Added configuration for stacking push notification on Android
= 13.04 =
disabling auto push notifications for custom post types (for now)
= 13.03 =
Fixed push notification not triggering on new post publish, this issue started in 13.01 plugin update, just the plugin update is enough, no need to create a new apk
= 13.02 =
added support for WPBakery Visual Composer plugins tags, Fixed wrong author issue (not reproducible on all websites), Fixed possible crashes and some more
= 13.00 =
Added Custom Post Type & Custom Taxonomy Support, Few possible crash issue fixes in android app
= 12.00 =
Updated target sdk version and added runtime permissions for android app, Showing push notification queue and Few other backend improvements
= 11.04 =
Fixed an installation issue, no need to create new apk for this release.
= 11.03 =
Fixed post details reload issue for lower versions (< Marshmallow). Fixed Don't send push notification flag issues. Fixed crash issue on clicking back while a link is loading. Added Cairo Bold font.
= 11.02 =
Book mark icon, faster loading of links, control on where to open the links
= 11.00 =
Added Top Slider Menu, configure menu from AndroApp->Configure tab, create new apk, test and publish to play store.
= 10.05 =
1. Fixed a php version compatibility issue for plugin, no need to publish new apk for this update
= 10.04 =
1. Added pull down refresh for android, you need to generate new build and publish
= 10.02 =
Added support for File Upload, Fixed image not showing when using scheme relative url (specially scene with jetpack photon), create new apk if facing these 2 issues, please test before you publish to play store.
= 10.01 =
Fixed issue in setting post content type as load url (introduced in 10.00) - only plugin update fixes it, no need to publish new apk (if you are already on 10.00)
= 10.00 =
Added menu icons support, need to publish new build
= 9.02 =
Fixed a backend issue to show push notification stats
= 9.01 =
fixed one bug in the last release, you need to publish new apk for using loadimages content type, else just update the plugin
= 9.00 =
Added new post content type i.e. SlideShow - to open all images in the post as slideshow
= 8.04 =
1. Minor backend enahancements, no need to create new apk
= 8.02 =
Minor fixes/changes in plugin at server side and fixed status bar color issue (you need to publish new apk if your app version is less than 8.01)
= 8.00 =
Major change for ads and comments: supporting banner ads from appnext, added facebook comments support, moved comments icon from menu to floating button.
= 7.04 =
Added ability to override post content type in AndroApp->Configure page and backend support for IOS app and in-built WP rest api in wordpress.
= 7.03 =
Added image zoom, share and save feature, fixed bottom banner ad not shown on post detail page issue, changed position of Saved Posts menu item and more..
= 7.02 =
Offline Save, Push notification cache, comments improvements and other bug fixes, need to create a new apk. publish your mobile app to google play store after testing.
= 6.09 =
New parallax effect for featured image on post detail page and few minor fixes.
= 6.08 =
Few hygiene updates as suggested by wordpress team.
= 6.07 =
Added support for nested menus, Handling mailto and tel links properly and fixed few bugs
= 6.06 =
Fixed a bug for bulk push notification send, sorry for the inconvenience
= 6.05 =
Added Bulk Send functionality for push notifications, improved performance, reduces server load, need to upgrade to firebase cloud messaging service first. Showing category link instead of author in News theme.
= 6.04 =
Updated to Firebase Cloud messaging api for push notification, please migrate your apps and add google app id in account settings tab. Better looking menu and few minor fixes.
= 6.03 =
Option to add app only html code/teax on post page, Fixed: Push notification redirecting to homepage when homepage type is set to single post/page
= 6.01 =
Fixed app not working on Ice CreamSandwich and Jelly Bean versions(< 5.0) issue, we recommend to publish a new build
= 6.00 =
Added Deep Linking support and option to update product quantity for woocommerce, please test your app (for deeplinking changes) before publishing on playstore. Just a heads up, annual renewal price will be increased to $60 from 1st July.
= 5.11 =
Added mopub ad provider support, you can use multiple ad networks through this, removed restriction from our side on using top/bottom/listview ads.
= 5.10 =
Minor fix
= 5.09 =
Moved AndroApp option from Settings menu to main menu and some changes for push notifications, not required to build new apk.
= 5.08 =
Added ability to show post from a particular category,tag,author,product_tag OR product_cat, Fixed one rendering issue on listing page, when there is only 1 or 2 posts to show.
= 5.07 =
Added fix for yuzo related post shortcode (when post content type is postprocessed), Updated logic to read excerpt from Yoast SEO plugin
= 5.06 = 
Added font support, you can select from few google fonts, utf-8 bom character issue fix(was reproducible on few sites only), some crash fixes
= 5.05 =
Fixed crash issue on opening external link (update required only if you are using 5.0.4 version, last release)
= 5.04 =
Minor improvements in Quick return, Fixed internal link issue opening in webview
= 5.03 =
Added Quick Return pattern, it hides/shows action bar based on user behavior, giving more device space for the content.
= 5.02 =
Added option to remove Google adsense units from post content (in Account Settings tab), Moving androapp to https, making your connection to androapp more secure.
= 5.01 =
Added wordpress audio/video support (no need of publishing new apk)
= 5.00 =
Fixed internal link not opening in webview issue, handling affiliate product buy button (for woocommerce), Fixed post title showing junk characters in push notification issue
= 4.09 =
Added RTL support, Added splash screen, Better resolution push notification image for news theme, fixed display name issue, using latest library for gcm registration
= 4.08 =
Fixed issue of admob interstitial not showing until appnext placement id is present, minor fix for woocommerce sites, publishing a new apk is recommended
= 4.07 =
Added product tag links support for woocommerce sites, new apk needed only for woocommerce enabled sites
= 4.06 =
Added search, it might not work properly if you are using any search plugin, please check and disabled in on Configure tab.
= 4.05 =
Added AppNext Ads Support, Ability to show interstitial ads on page swipes, Option to change top and bottom ad unit types, Fixed cart icon visible on Comments Settings Screen, Few more fixes
= 4.04 =
Added option to show a post or page on homepage, Showing Vendor info for woocommerce app with WC MArketplace plugin.
= 4.03 =
Fixed comments issue(caused in last build), all the users who are using app version 4.0.0 and having comments option enabled, please update the app. option to display list of pages on homepage(instead of posts)
= 4.02 =
New year bonanza !! Finally we support woocommerce, sticky ads on post pages too, more push notification control, check renewal date, and moving over to support devices with API level > 11(i.e. Honeycomb or later)
= 4.00 = 
Loading icon on homepage screen, it gives the correct error message for the new user, Gmail like Swipe Left Right feature on post pages, Better looking share, menu and comments icons, Animations on every transition, Option to set status bar color, Task Description color same as app action bar background color, Some background changes to make the app faster, it keeps less data in memory and releases unused resources while moving in-out from one screen to another
= 3.25 =
Added curl check for push notification, no need to new build
= 3.24 = 
Added Restore to default settings options in the plugin, no change in the app
= 3.22 = 
Fixed issue in creating builds for new users. Existing users no change in app or functionality
= 3.20 =
Small fix for creating the build for new users, no change in the app
= 3.18 = 
Fixed an issue where new users was seeing a blank screen on first time, app was working fine for second start. So if you are on app version 2.0.7(you can see your app version in your google play store account), please publish a new build with version 2.0.8 or more.
= 3.17 =
Added option to change texts used in the app (you can use your own language now), you can change the text on the fly. Tracking outbound links. Added few more ad size options
