// JavaScript Document
jQuery(window).load(function(){
	jQuery(".moonray-form *[data-required=true]").each(function(){
		$label=jQuery(".moonray-form label[for="+jQuery(this).attr("id")+"]");
		if($label.length>0){
			if($label.find(".moonray_required").length==0)
				$label.html($label.html()+"<span class='mandatory'>*</span>");
			else
				$label.find(".moonray_required").addClass("mandatory");
		}
	});
	jQuery(".moonray_forms .required").each(function(){
		$label=jQuery(".moonray_forms label[for="+jQuery(this).attr("name")+"]");
		if($label.length>0){
			if($label.find(".moonray_required").length==0)
				$label.html($label.html()+"<span class='mandatory'>*</span>");
			else
				$label.find(".moonray_required").addClass("mandatory");
		}
		else{
			$placeholder=jQuery(this).attr("placeholder");
			if (typeof $placeholder != 'undefined')
				jQuery(this).attr("placeholder", $placeholder+" *");
		}
		
	});
	jQuery(".moonray-form *[data-required=true], .moonray_forms .required").blur(function(){
		$this=jQuery(this);
		if($this.val()==""){
			if($this.attr("rel")==undefined){
				$id=$this.attr("name")+Math.floor((Math.random() * 10000) + 1);
				$this.attr("rel", $id);
				$this.after('<div class="custom-error-message" id="'+$id+'">This field is required.</div>');
			}
			else{
				jQuery("#"+$this.attr("rel")).show();
			}
		}
		else{
			if($this.attr("rel")!=""){
				jQuery("#"+$this.attr("rel")).hide();
			}
		}
	});
	jQuery("form").submit(function(){
		$err=new Array();
		jQuery(".moonray-form-error-message, div.mr_error_wrapper").each(function(){
			if(jQuery(this).attr("visibility")!="hidden"){
				$text=jQuery(this).text();
				if($text=="Please complete this mandatory field." || $text=="This field is required.")
					$text="Please complete all mandatory field markeds red.";
				if($err.indexOf($text)===-1){
					$err[$err.length]=$text;
				}
			}
		});
		if($err.length>0){
			$err=$err.join("<br />");
			if(jQuery(".moonray-form .form_error, .moonray_forms .form_error").length>0){
				jQuery(".moonray-form .form_error, .moonray_forms .form_error").html($err);
			}
			else{
				jQuery(".moonray-form, .moonray_forms").prepend("<div class='form_error'>"+$err+"</div>");
			}
			jQuery(".custom-error-message").remove();
		}
	});
});