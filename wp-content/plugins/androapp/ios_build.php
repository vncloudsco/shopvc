
<?php
$accountSettings = '<a href="?page=pw_mobile_app_options&tab=androapp_account_settings">'.
				__('Account Settings','androapp').'</a>';

$lookFeelSettings = '<a href="?page=pw_mobile_app_options&tab=pw-mobile-build-options">'.
				__('Look & Feel','androapp').'</a>';

$buildOptions = get_option($this->build_option_name);
?>
We now provide IOS build support, creating and publishing IOS app is little trickier and time consuming than Android App,
so we will recommend creating IOS app only after you have tested and verified Android App.

<h3>Please follow these steps to get the IOS app</h3>

<ol>
    <li>Get your Android App by following Get Started tab, verify that it is working fine. As, if it is working fine, most likely IOS app will also work without any issues</li>
    <li>Checkout <a href="https://itunes.apple.com/app/id1229480101" target="_blank" >this sample app</a> on your IOS device
    </li>
    <li>Add your <a href="https://androapp.mobi/blog/create-firebase-app-ios/247" target="_blank" ><b>Firebase App ID</b></a> for IOS App in <?php echo $accountSettings;?> tab</li>
    <li>Create new Google Analytics account for tracking IOS App and enter <b>tracking ID</b> for IOS app in <?php echo $accountSettings;?> tab</li>
    <li>Trigger one android build after from <?php echo $lookFeelSettings;?> tab. (this is to push the ids you entered in earlier steps)</li>
</ol>

<h3>
Once you are done with above changes, you are ready to publish your IOS app, there are two options to publish your app
</h3>
<h3>Publish YourSelf (you will need macbook for step 2,3 & 9)</h3>
<ol>
    <li>Create new <a href="https://developer.apple.com/" target="_blank" >Apple developer account</a>, apple has a fee of $100/year</li>
    <li>Create Distribution Certificate <a href="https://androapp.mobi/blog/create-distribution-certificate-ios-app/296" target="_blank" >https://androapp.mobi/blog/create-distribution-certificate-ios-app/296</a></li>
    <li>Export the certificate in .p12 file <a href="https://androapp.mobi/blog/export-publicprivate-key-macbook/358" target="_blank" >https://androapp.mobi/blog/export-publicprivate-key-macbook/358 </a></li>
    <li>Create a Wildcard App ID: <a href="https://androapp.mobi/blog/generate-wildcard-app-id/332" target="_blank" >https://androapp.mobi/blog/generate-wildcard-app-id/332</a></li>
    <li>Create a provisioning profile for WildCard App <a href="https://androapp.mobi/blog/create-provisioning-profile/289" target="_blank"> https://androapp.mobi/blog/create-provisioning-profile/289</a>
    <li>Create App ID for this website's app: <a href="https://androapp.mobi/blog/create-app-id/283" target="_blank" >https://androapp.mobi/blog/create-app-id/283</a></li>
    <li>Create provisioning profile for this website's APP ID: <a href="https://androapp.mobi/blog/create-provisioning-profile/289" target="_blank"> https://androapp.mobi/blog/create-provisioning-profile/289</a></li>
    <li>email your website link, team ID( <a href="https://androapp.mobi/blog/find-apple-developer-account-team-id/311" target="_blank">how to get teamID</a>), exported certificate (.p12 file) and two provisioning profiles(.mobileprovision file) to contact@androapp.mobi</li>
    <li>we will generate your IOS build, you should receive an email in 48 hours. meanwhile, Follow <a target="_blank" href="https://androapp.mobi/blog/how-to-upload-app-to-ios-app-store/267"> these instructions</a> to create app in itunes account</li>
    <li>Create auth keys(.p8 files) for push notification <a href="https://androapp.mobi/blog/create-apn-auth-keys-ios-push-notification/396" target="_blank">https://androapp.mobi/blog/create-apn-auth-keys-ios-push-notification/396</a> and upload them to your firebase project <a href="https://androapp.mobi/blog/upload-apn-auth-keys-firebase-project/402" target="_blank">https://androapp.mobi/blog/upload-apn-auth-keys-firebase-project/402</a> </li>
    
</ol>
<b>Note:-</b> first 5 steps to be done once per apple account, so if you have multiple apps to submit, start from step 6 for next app.

<h3>OR, Let us do the hardwork</h3>
For any reasons, if you are not able to submit the app yourself, we can submit your app from your own account
<ol>
    <li>Create new <a href="https://developer.apple.com/" target="_blank" >Apple developer account</a>, apple has a fee of $100/year</li>
    <li>Share your apple developer account credentials with us</li>
    <li>Pay $30 for our manual efforts, you can pay <a href="https://www.payumoney.com/store/product/3e6be052f8c4d8e1e7468f5013cfe27c" target="_blank" >via payumoney</a> or drop us an email for paypal payment note</li>
    <li>Just in case you app is rejected by Apple, we will adjust this $30 amount to your android app renewal, but will not be able to refund</li>
    <li>We will need some of your app details, please fill <a href="https://goo.gl/forms/wfyZVc285Kv1huWr2" target="_blank">this google form</a></li>
</ol>
