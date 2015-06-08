!function(a){Craft.Updater=Garnish.Base.extend({$graphic:null,$status:null,$errorDetails:null,data:null,init:function(b,c){return this.$graphic=a("#graphic"),this.$status=a("#status"),b?(this.data={handle:b,manualUpdate:c},void this.postActionRequest("update/prepare")):void this.showError(Craft.t("Unable to determine what to update."))},updateStatus:function(a){this.$status.html(a)},showError:function(a){this.updateStatus(a),this.$graphic.addClass("error")},postActionRequest:function(b){var c={data:this.data};Craft.postActionRequest(b,c,a.proxy(function(a,b,c){"success"==b&&a.alive?this.onSuccessResponse(a):this.onErrorResponse(c)},this),{complete:a.noop})},onSuccessResponse:function(a){if(a.data&&(this.data=a.data),a.errorDetails&&(this.$errorDetails=a.errorDetails),a.nextStatus&&this.updateStatus(a.nextStatus),a.nextAction&&this.postActionRequest(a.nextAction),a.finished){var b=!1;a.rollBack&&(b=!0),this.onFinish(a.returnUrl,b)}},onErrorResponse:function(a){this.$graphic.addClass("error");var b="<p>"+Craft.t("A fatal error has occurred:")+'</p><div id="error" class="code"><p><strong class="code">'+Craft.t("Status:")+"</strong> "+Craft.escapeHtml(a.statusText)+'</p><p><strong class="code">'+Craft.t("Response:")+"</strong> "+Craft.escapeHtml(a.responseText)+'</p></div><a class="btn submit big" href="mailto:support@buildwithcraft.com?subject='+encodeURIComponent("Craft update failure")+"&body="+encodeURIComponent("Describe what happened here.\n\n-----------------------------------------------------------\n\nStatus: "+a.statusText+"\n\nResponse: "+a.responseText)+'">'+Craft.t("Send for help")+"</a>";this.updateStatus(b)},onFinish:function(a,b){if(this.$errorDetails){this.$graphic.addClass("error");var c=Craft.t("Craft was unable to install this update :(")+"<br /><p>";c+=b?Craft.t("The site has been restored to the state it was in before the attempted update.")+"</p><br /><p>":Craft.t("No files have been updated and the database has not been touched.")+"</p><br /><p>",c+=this.$errorDetails+"</p>",this.updateStatus(c)}else this.updateStatus(Craft.t("All done!")),this.$graphic.addClass("success"),setTimeout(function(){a?window.location=Craft.getUrl(a):window.location=Craft.getUrl("dashboard")},500)}})}(jQuery);
//# sourceMappingURL=update.js.map