{crmAPI var='pu_field' entity='CustomField' action='getsingle' sequential=1 name='Pu_Overview'}
{crmAPI var='pu_value' entity='Contact' action='getsingle' version='3' id="$contactId" return="custom_`$pu_field.id`"}
{assign var=pu_field_value value="custom_`$pu_field.id`"}
{assign var=pu value="`$pu_value.$pu_field_value`"}
{if $pu > 0}
 {crmAPI var='pudesc' entity='OptionValue' action='getsingle' sequential=1 option_group_id=$pu_field.option_group_id value=$pu return='label'}
 {crmAPI var='pu_n_field' entity='CustomField' action='getsingle' sequential=1 name='Pu_Contact'}
 {crmAPI var='pu_n_value' entity='Contact' action='getsingle' version='3' id="$contactId" return="custom_`$pu_n_field.id`"}
 {assign var=pu_n_field_value value="custom_`$pu_n_field.id`"}
 {assign var=pu_n value="`$pu_n_value.$pu_n_field_value`"}
{else}
 {assign var=pu_n_field_value value="0"}
 {assign var=pu_n value=""}
{/if}

<div id="pufield" class="crm-inline-edit-container"><span id="pusign" style="position:relative;float:right;top:-30px">&#8857;<span></div>
{literal}
<style>
  .Pu_fields textarea { width: 90%;}
  .Pu_fields crm-form-select { width: 30% !important;}

</style>

<script>
function set_pu_value(){
  CRM.api('Contact', 'get', {'sequential': 1, 'id': {/literal}{$contactId}{literal}, 'return': '{/literal}{$pu_field_value}{literal}'},
     { success: function(data) {   
        pu=data.values[0]["{/literal}{$pu_field_value}{literal}"];
        if (!pu) {
	  pusize = "27";
          putext = "&#8857;";
          pudeco = "none";
          pucolor="#ffff00";
        }
        else if (pu == 1) {
          pusize = "100";
          putext = "*";
          pudeco = "";
          pucolor = "#00ff00";
        }
        else {
          pusize = 7+(pu*10);
          putext = "&#8857;";
          pudeco = "none";
          pucolor="#666666";
        }
        cj("#pusign").css("font-size",pusize+"px").css("text-decoration", pudeco).css("color", pucolor).html(putext);
      }
     }
   );

}
cj(document).ready(function($) {
 cj("#pufield").append(cj(".Pu_fields"));
 cj(".Pu_fields").css("width", "50%").css("float","right").css("top","-30px").css("position", "relative").hide();
 cj("#pufield").hover(function(){
  cj(".Pu_fields").show();   
 }, function(){
  if(cj("#pufield .crm-inline-edit.form").length==0){
   cj(".Pu_fields").hide();
  }
 });
 cj("#crm-container").prepend(cj("#pufield"));
 cj(".crm-summary-block").load(function(){
   set_pu_value();
   if (!$('#pufield').is(':hover')) {
     cj(".Pu_fields").hide();
   }

 });
 set_pu_value();
});


</script>{/literal}
