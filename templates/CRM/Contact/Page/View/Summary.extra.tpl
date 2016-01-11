{crmAPI var='pu_field' entity='CustomField' action='getsingle' sequential=1 name='Pu_Overview'}
{crmAPI var='pu_activity_field' entity='CustomField' action='getsingle' sequential=1 name='new_pu_value'}
{assign var=pu_activity_field_value value="custom_`$pu_activity_field.id`"}
{crmAPI var='pu_activity_desc_field' entity='CustomField' action='getsingle' sequential=1 name='new_pu_description'}
{assign var=pu_activity_desc_field_value value="custom_`$pu_activity_desc_field.id`"}
{crmAPI var='pu_activity_aut_field' entity='CustomField' action='getsingle' sequential=1 name='Pu_Automation'}
{assign var=pu_activity_aut_field_value value="custom_`$pu_activity_aut_field.id`"}
{crmAPI var='pu_activity_how_field' entity='CustomField' action='getsingle' sequential=1 name='new_pu_how'}
{assign var=pu_activity_how_field_value value="custom_`$pu_activity_how_field.id`"}
{crmAPI var='pu_activity_action_field' entity='CustomField' action='getsingle' sequential=1 name='new_pu_action'}
{assign var=pu_activity_action_field_value value="custom_`$pu_activity_action_field.id`"}


{crmAPI var='pu_value' entity='Contact' action='getsingle' version='3' id="$contactId" return="custom_`$pu_field.id`"}
{assign var=pu_field_value value="custom_`$pu_field.id`"}
{crmAPI var='pu_action' entity='CustomField' action='getsingle' sequential=1 name='Pu_Action'}
{crmAPI var='pu_action_value' entity='Contact' action='getsingle' version='3' id="$contactId" return="custom_`$pu_action.id`"}
{assign var=pu_field_action value="custom_`$pu_action.id`"}
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
  .Pu_fields .crm-form-select { width: 30% !important;}

</style>

<script>


function set_pu(){
    puField = "{/literal}{$pu_activity_field_value}{literal}";
    puDescField = "{/literal}{$pu_activity_desc_field_value}{literal}";
    puAutField = "{/literal}{$pu_activity_aut_field_value}{literal}";
    puHowField = "{/literal}{$pu_activity_how_field_value}{literal}";
    puActionField = "{/literal}{$pu_activity_action_field_value}{literal}";
    CRM.api3('Activity', 'get', {
        "sequential": 1,
        "target_contact_id": {/literal}{$contactId}{literal},
        "status_id":"Scheduled",
        "activity_type_id":"puChanges",
        "return": "subject,"+puField+","+puDescField+","+puAutField+","+puActionField+","+puActionField
    }).done(function(result) {
        puValue = 0;
        for (i = 0; i < result.values.length; i++)
        {
            activity = result.values[i];
            puValue = puValue + parseInt(activity[puField]);
            console.log(activity[puField]+":"+activity[puDescField]+";"+activity[puActionField]+":"+activity[puHowField]+" - "+activity[puAutField]);
            //  console.log(result.values[i]);
        }
        set_pu_value(puValue);

    });

}

function set_pu_value(pu){
  CRM.api('Contact', 'get', {'sequential': 1, 'id': {/literal}{$contactId}{literal}, 'return': '{/literal}{$pu_field_value},{$pu_field_action}{literal}'},
     { success: function(data) {   
      //  pu=data.values[0]["{/literal}{$pu_field_value}{literal}"];
        puaction=data.values[0]["{/literal}{$pu_field_action}{literal}"];
        if (!pu) {
	  pusize = "27";
          putext = "&#8857";
          pudeco = "none";
          pucolor="#bbbb00";
        }
        else if (pu == 1) {
          pusize = "100";
          putext = "*";
          pudeco = "";
          pucolor = "#666666";
        }
        else {
          if (pu > 10) pu = 10;
          pusize = 7+(pu*10);
          putext = "&#8857;";
          pudeco = "none";
          pucolor="#666666";
          if (puaction == 1) pucolor = "#0000bb";
          else if (puaction == 2) pucolor = "#bb0000";
          else if (puaction == 3) pucolor = "#00bb00";
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
   set_pu();
   if (!$('#pufield').is(':hover')) {
     cj(".Pu_fields").hide();
   }

 });
 set_pu();


});


</script>{/literal}
