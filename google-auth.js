
// Draekko https://github.com/draekko/google-authenticator-for-woocommerce
// JS file to add input field for woocommerce login form.
// GPLv3
// Code borrowed from Two Factor Authetification plugin. 

jQuery(document).ready(function() {
	//See if WooCommerce login form is present
	if(jQuery('form.login').size() > 0){
	    var ga_wc_user_field = jQuery('[name=username]');
		var ga_wc_submit_btn = jQuery('[name=login]');

		var ga_wc_otp_btn = document.createElement('button');
		ga_wc_otp_btn.id = 'google_auth_otp_button';
		ga_wc_otp_btn.className = 'button button-large button-primary';
		ga_wc_otp_btn.onclick = function(){ return gaChangeToInput(); };
		ga_wc_otp_btn.style.styleFloat = 'none';
		ga_wc_otp_btn.style.cssFloat = 'none';
		
		var ga_wc_btn_text = document.createTextNode(ga_wc_settings.click_to_enter_otp);
		ga_wc_otp_btn.appendChild(ga_wc_btn_text);
		ga_wc_otp_btn.style.width = '100%';
		
		var ga_wc_p = document.createElement('p');
		ga_wc_p.id = 'ga_wc_holder';
		ga_wc_p.style.marginTop = '-12px';
		var lbl = document.createElement('label');
		lbl.id = 'ga_label_wc_holder';
		lbl.style.width = '100%';
		var lbl_text = document.createTextNode(ga_wc_settings.google_otp);
		lbl.appendChild(lbl_text);
		
		lbl.appendChild(ga_wc_otp_btn);
		ga_wc_p.appendChild(lbl);
		gaAddToForm(ga_wc_p);
	}
	
	function gaChangeToInput() {
		//Check so a username is entered.
		if(ga_wc_user_field.val().length < 1) {
			alert(ga_wc_settings.enter_username_first);
			return false;
		}
		
		jQuery.post(
			ga_wc_settings.ajaxurl,
			{
				action : 'ga-init-otp',
				user : ga_wc_user_field.val()
			},
			function( response ) {
			}
		);
		
		var p = document.getElementById('ga_wc_holder');
		p.style.marginTop = '-12px';
		var lbl = document.createElement('label');
		lbl.for = 'user-email';
		lbl.style.width = '100%';
		var lbl_text = document.createTextNode(ga_wc_settings.google_otp);
		lbl.appendChild(lbl_text);
		
		var ga_field = document.createElement('input');
		ga_field.type = 'text';
		ga_field.id = 'user-email';
		ga_field.name = 'googleotp';
		ga_field.className = 'input input-text';
		ga_field.setAttribute("autocomplete", "off"); 
		ga_field.setAttribute("style", "ime-mode: inactive;"); 
		lbl.appendChild(ga_field);
		
		//Remove button
		p.removeChild(document.getElementById('ga_label_wc_holder'));
		
		//Add text and input field
		p.appendChild(lbl);
		ga_field.focus();
		
		//Enable regular submit button
		ga_wc_submit_btn.removeAttr("disabled");
	}
	
	function gaAddToForm(p) {
		jQuery(p).insertBefore(ga_wc_submit_btn);
	}
});
