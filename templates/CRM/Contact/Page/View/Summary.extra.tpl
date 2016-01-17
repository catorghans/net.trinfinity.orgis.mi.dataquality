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
  .pu_activity.automated {
      background-color: #F6F6F2;
  }
  .pu_activity.manual{
      cursor: pointer;
  }
  #pu_activities {
     border: 1px solid black;
      float:right;
      width: 70%;
      top: -30px;
      position: relative;
  }
  #pu_add_sign {
     padding: 3px;
     cursor: pointer;
  }

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
        if (document.contains(document.getElementById("pu_activities"))) {
            document.getElementById("pu_activities").remove();
        }
        puhtml = document.createElement("div");
        puhtml.setAttribute("id","pu_activities");
        puheader = document.createElement("div");


        puaddsign = document.createElement("span");
        puaddsign.setAttribute("id", "pu_add_sign");
        puaddsign.innerHTML = "+";
        puaddsign.setAttribute("title", "Add new Pu activity");
        puheader.appendChild(puaddsign);
        puheadertext = document.createElement("span");
        puheadertext.innerHTML = " <b>Pu Fields</b>";
        puheader.appendChild(puheadertext);

        puhtml.appendChild(puheader);
        pumanual = document.createElement("div");
        puautomated = document.createElement("div");

        for (i = 0; i < result.values.length; i++)
        {
            puactivity = document.createElement("div");
            activity = result.values[i];
            puValue = puValue + parseInt(activity[puField]);
            console.log(activity[puField]+":"+activity[puDescField]+";"+activity[puActionField]+":"+activity[puHowField]+" - "+activity[puAutField]);
            value = activity[puField];
            action = activity[puActionField];
            strsign = get_mini_pu_value(value, action);

            innerHTML = strsign+" "+activity[puDescField];
            if (activity[puHowField]){
                innerHTML = innerHTML+": "+activity[puHowField];
            }

            puactivity.innerHTML = innerHTML;
            puclass = "pu_activity manual";
            activitytext = " Click to Edit";
            parentdiv = pumanual;

            if (activity[puAutField] == 1){
                puclass = "pu_activity automated";
                activitytext = " Automated";
                parentdiv = puautomated;
            }

            actionclass = "";
            title = "";
            switch (action){
                case "1": actionclass = " solve";
                    break;
                case "2": actionclass = " avoid";
                    break;
                case "3": actionclass = " acknowledge";
            }
            switch (value){
                case "1": putext = "A bit pu";
                    break;
                case "2": putext = "More pu";
                    break;
                case "3": putext = "A lot of pu";
                    break;

            }
            title = putext + " - "+activitytext;

            puactivity.setAttribute("class", puclass+actionclass);
            puactivity.setAttribute("title", title);
            parentdiv.appendChild(puactivity);
            //  console.log(result.values[i]);
        }
        puhtml.appendChild(pumanual);
        puhtml.appendChild(puautomated);
        puhtml.style.display = "none";
        pufield = document.getElementById("pufield");
        pufield.appendChild(puhtml);
        set_pu_value(puValue);

    });

}

function get_mini_pu_value(pu, puaction) {
  var ret = "";
    pusize = "15";
    pucolor = "#000000";
    putext = "&#8857;";
    putitle = "";
    switch (pu){
        case "1":
            break;
        case "2":
            pusize = "19";
            break;
        case "3":
            pusize = "24";
    }
    switch (puaction){
        case "1":
            pucolor = "#0000bb";
            putitle = "solve";
            break;
        case "2":
            pucolor = "#bb0000";
            putitle = "avoid";
            break;
        case "3":
            pucolor = "#00bb00";
            putitle = "acknowlegde";
    }
    ret = "<span style='font-size:"+pusize+"px;color:"+pucolor+";' title='"+putitle+"'>"+putext+"</span>";
    return ret;

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

    cj("#pu_activities").css("width", "50%").css("float","right").css("top","-30px").css("position", "relative").hide();

 cj("#pufield").hover(function(){
  cj(".Pu_fields").hide();
     cj("#pu_activities").show();
 }, function(){
  if(cj("#pufield .crm-inline-edit.form").length==0){
   cj(".Pu_fields").hide();
      cj("#pu_activities").hide();
  }
 });
 cj("#crm-container").prepend(cj("#pufield"));
 cj(".crm-summary-block").load(function(){
   set_pu();
   if (!$('#pufield').is(':hover')) {
     cj(".Pu_fields").hide();
       cj("#pu_activities").hide();
   }

 });
 set_pu();


});


</script>{/literal}
