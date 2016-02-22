{crmAPI var='pu_activity_field' entity='CustomField' action='getsingle' sequential=1 name='new_pu_value'}
{assign var=pu_activity_field_value value="custom_`$pu_activity_field.id`"}
{crmAPI var='pu_activity_desc_field' entity='CustomField' action='getsingle' sequential=1 name='new_pu_description'}
{assign var=pu_activity_desc_field_value value="custom_`$pu_activity_desc_field.id`"}
{crmAPI var='pu_activity_aut_field' entity='CustomField' action='getsingle' sequential=1 name='Pu_Automation'}
{assign var=pu_activity_aut_field_value value="custom_`$pu_activity_aut_field.id`"}
{crmAPI var='pu_activity_aut_type_field' entity='CustomField' action='getsingle' sequential=1 name='Automation_Type'}
{assign var=pu_activity_aut_type_field_value value="custom_`$pu_activity_aut_type_field.id`"}
{crmAPI var='pu_activity_how_field' entity='CustomField' action='getsingle' sequential=1 name='new_pu_how'}
{assign var=pu_activity_how_field_value value="custom_`$pu_activity_how_field.id`"}
{crmAPI var='pu_activity_action_field' entity='CustomField' action='getsingle' sequential=1 name='new_pu_action'}
{assign var=pu_activity_action_field_value value="custom_`$pu_activity_action_field.id`"}
{crmAPI var='pu_manual_close_field' entity='CustomField' action='getsingle' sequential=1 name='Pu_Manual_Close'}
{assign var=pu_manual_close_field_value value="custom_`$pu_manual_close_field.id`"}



<div id="pufield" class="crm-inline-edit-container"><span id="pusign" style="position:relative;float:right;top:-30px">&#8857;<span></div>
{literal}
<style>
  .Pu_fields textarea { width: 90%;}
  .Pu_fields .crm-form-select { width: 30% !important;}
  .pu_activity.automated {
      background-color: #F6F6F2;
  }
  .pu_activity.edit{
      cursor: pointer;

  }
  .pu_activity.edit:hover {
      border: 2px dashed #D3D3D3;
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
  #pu_header:hover {
      cursor: pointer;
      border: 2px dashed #D3D3D3;
  }

</style>

<script>

    function create_pu_form(activity_id, readonly){
        contact_id = "{/literal}{$contactId}{literal}";

        puvalue = "";
        pudescription = "";
        puaction = "";
        putype = "0"; //manual
        puhow = "";
        puField = "{/literal}{$pu_activity_field_value}{literal}";
        puDescField = "{/literal}{$pu_activity_desc_field_value}{literal}";
        puAutField = "{/literal}{$pu_activity_aut_field_value}{literal}";
        puHowField = "{/literal}{$pu_activity_how_field_value}{literal}";
        puActionField = "{/literal}{$pu_activity_action_field_value}{literal}";


        if (activity_id){ //edit

            //load activity
            CRM.api3('Activity', 'get', {
                "sequential": 1,
                "target_contact_id": {/literal}{$contactId}{literal},
                "status_id":"Scheduled",
                "activity_type_id":"puChanges",
                "id": activity_id,
                "return": "subject,"+puField+","+puDescField+","+puAutField+","+puActionField+","+puActionField
            }).done(function(result) {
                // 1 expected
                if (result["is_error"] == "0" && result["count"] == "1" ){
                    activity = result["values"][0];
                    puvalue = activity[puField];
                    pudescription = activity[puDescField];
                    puhow = activity[puHowField];
                    puaction = activity[puActionField];
                    create_pu_html_form(activity_id, puvalue, pudescription, puaction, puhow, readonly);
                }
            });
        }
        else { //add
            create_pu_html_form(activity_id, puvalue, pudescription, puaction, puhow, false);
        }

        //create form

    }
    function create_pu_html_form(id, puvalue, pudescription, puaction, puhow, readonly){
        console.log ( "activity "+id+" "+puvalue+" "+pudescription+" "+puhow+" "+puaction);


        disabled = "";
        if (readonly){
            disabled = "disabled = 'disabled'";
        }
        puform = document.createElement("form");
        puform.setAttribute("id", "puform");

        putable = document.createElement("table");
        putable.setAttribute("class", "crm-inline-edit-form");

        puheader = document.createElement("tr");
        puheadercell = document.createElement("th");
        puheadercell.innerHTML = "<b>Pu Fields</b>" +
               ' <div class="crm-inline-button"> '+
                '<span class="crm-button crm-button-type-upload crm-button_qf_CustomData_upload crm-icon-button">'+
                '<span class="crm-button-icon ui-icon-check"> </span>          <input class="crm-form-submit default validate" accesskey="S" crm-icon="check" name="_qf_CustomData_upload" value="Save" id="_qf_CustomData_upload" type="submit">'+
                '</span><span class="crm-button crm-button-type-cancel crm-button_qf_CustomData_cancel crm-icon-button">'+
                '<span class="crm-button-icon ui-icon-close"> </span>          <input class="crm-form-submit cancel" crm-icon="close" name="_qf_CustomData_cancel" value="Cancel" id="_qf_CustomData_cancel" type="submit" onclick="cancel_pu_form();return false;">'+
                '</span></div>'+
                "<div class='messages help'><p>Pu is what you do not know or understand.</p></div>";
        puheadercell.setAttribute("colspan", "2");

        puheader.appendChild(puheadercell);
        putable.appendChild(puheader);

        putr = document.createElement("tr");
        putdlabel = document.createElement("td");
        putdlabel.innerHTML = "<label>Pu</label>";

        putdfield = document.createElement("td");

        puhtmlvalue = "<select id='puform_value' "+disabled+">";
        s = { 1: "small", 2: "medium", 3: "large" }
        for (i = 1; i <= 3; i++){
            selected = "";
            if (parseInt(puvalue) == i){
                selected = " selected";
            }
            puhtmlvalue += "<option value='"+i+"' "+selected+">"+s[i]+"</option>";
        }
        puhtmlvalue += "</select>";

        putdfield.innerHTML = puhtmlvalue;

        putr.appendChild(putdlabel);
        putr.appendChild(putdfield);

        putable.appendChild(putr);

        putr = document.createElement("tr");
        putdlabel = document.createElement("td");
        putdlabel.innerHTML = "<label>Description</label>";

        putdfield = document.createElement("td");

        el = document.createElement("textarea");
        el.setAttribute("id", "puform_description");
        if (readonly){
            el.setAttribute("disabled", "disabled");
        }
        el.innerHTML = pudescription;

        putdfield.appendChild(el);

        putr.appendChild(putdlabel);
        putr.appendChild(putdfield);

        putable.appendChild(putr);


        putr = document.createElement("tr");
        putdlabel = document.createElement("td");
        putdlabel.innerHTML = "<label>Action</label>";

        putdfield = document.createElement("td");

        puhtmlvalue = "<select id='puform_action' "+disabled+">";

        s = { 1: "solve", 2: "circumvent", 3: "acknowledge" }
        puhtmlvalue += "<option value=''></option>";

        for (i = 1; i <= 3; i++){
            selected = "";
            if (parseInt(puaction) == i){
                selected = " selected";
            }
            puhtmlvalue += "<option value='"+i+"' "+selected+">"+s[i]+"</option>";
        }
        puhtmlvalue += "</select>";


        putdfield.innerHTML = puhtmlvalue;

        putr.appendChild(putdlabel);
        putr.appendChild(putdfield);

        putable.appendChild(putr);

        putr = document.createElement("tr");
        putdlabel = document.createElement("td");
        putdlabel.innerHTML = "<label>How</label>";

        putdfield = document.createElement("td");

        el = document.createElement("textarea");
        el.setAttribute("id", "puform_how");
        if (readonly){
            el.setAttribute("disabled", "disabled");
        }
        el.innerHTML = puhow;

        putdfield.appendChild(el);

        putr.appendChild(putdlabel);
        putr.appendChild(putdfield);

        putable.appendChild(putr);

        putr = document.createElement("tr");
        putdlabel = document.createElement("td");
        putdlabel.innerHTML = "<label>Closed</label>";

        putdfield = document.createElement("td");

        el = document.createElement("input");
        el.type = "checkbox";
        el.value = "1";
        el.setAttribute("id", "puform_status");

        putdfield.appendChild(el);

        putr.appendChild(putdlabel);
        putr.appendChild(putdfield);

        putable.appendChild(putr);

        puform.appendChild(putable);
        puactivities = document.getElementById("pu_activities");
        puactivities.innerHTML= "";

        puactivities.appendChild(puform);

        puform.setAttribute("onsubmit", "process_pu_form('"+id+"'); return false;");
    }
 function process_pu_form(id){
     contact_id = "{/literal}{$contactId}{literal}";
     puField = "{/literal}{$pu_activity_field_value}{literal}";
     puDescField = "{/literal}{$pu_activity_desc_field_value}{literal}";
     puAutField = "{/literal}{$pu_activity_aut_field_value}{literal}";
     puHowField = "{/literal}{$pu_activity_how_field_value}{literal}";
     puActionField = "{/literal}{$pu_activity_action_field_value}{literal}";

     puform = document.getElementById("puform");
     puvalue = document.getElementById("puform_value").value;
     pudescription = document.getElementById("puform_description").value;
     puaction = document.getElementById("puform_action").value;
     puhow = document.getElementById("puform_how").value;
     pusolved = document.getElementById("puform_status").checked;
     pustatus = "Scheduled";
     if (pusolved) {
         pustatus = "Completed";
     }

     params = {
         "activity_type_id":"puChanges",
         "target_contact_id" : contact_id,
         "status_id": pustatus,
         "subject": pudescription,
     }
     params[puField] = puvalue;
     params[puDescField] = pudescription;
     params[puHowField] = puhow;
     params[puActionField] = puaction;
     if (parseInt(id) > 0) {
         params["id"] = id;
     }
     console.log(params);

     CRM.api3('Activity', 'create', params).done(function(result) {
         console.log(result);
         pu_activities = document.getElementById("pu_activities");
         pu_activities.innerHTML = "";
         set_pu();
     });
 }


    function cancel_pu_form(){
        pu_activities = document.getElementById("pu_activities");
        pu_activities.innerHTML = "";
        set_pu();

    }
function set_pu(){
    puField = "{/literal}{$pu_activity_field_value}{literal}";
    puDescField = "{/literal}{$pu_activity_desc_field_value}{literal}";
    puAutField = "{/literal}{$pu_activity_aut_field_value}{literal}";
    puHowField = "{/literal}{$pu_activity_how_field_value}{literal}";
    puActionField = "{/literal}{$pu_activity_action_field_value}{literal}";
    contact_id = "{/literal}{$contactId}{literal}";
    puAutTypeField = "{/literal}{$pu_activity_aut_type_field_value}{literal}";
    puManualSolvField = "{/literal}{$pu_manual_close_field_value}{literal}";

    CRM.api3('Activity', 'get', {
        "sequential": 1,
        "target_contact_id": {/literal}{$contactId}{literal},
        "status_id":"Scheduled",
        "activity_type_id":"puChanges",
        "return": "subject,"+puField+","+puDescField+","+puAutField+","+puActionField+","+puActionField+","+puAutTypeField
    }).done(function(result) {
        puValue = 0;
        puAction = 0;
        puActionA = { };
        puActionA[1] = 0;
        puActionA[2] = 0;
        puActionA[3] = 0;


        if (document.getElementById("pu_activities")){
            child = document.getElementById("pu_activities");
            child.parentNode.removeChild(child);
        }
        puhtml = document.createElement("div");
        puhtml.setAttribute("id","pu_activities");
        puheader = document.createElement("div");
        puheader.setAttribute("id", "pu_header");


        puaddsign = document.createElement("span");
        puaddsign.setAttribute("id", "pu_add_sign");
        puaddsign.innerHTML = "+";
        puheader.setAttribute("title", "Add new Pu activity");
        puheader.appendChild(puaddsign);
        puheadertext = document.createElement("span");
        puheadertext.innerHTML = " <b>Pu Fields</b>";
        puheader.appendChild(puheadertext);
        puheader.setAttribute("onclick", "create_pu_form(null, false);");

        puhtml.appendChild(puheader);
        pumanual = document.createElement("div");
        puautomated = document.createElement("div");

        for (i = 0; i < result.values.length; i++)
        {
            puactivity = document.createElement("div");
            activity = result.values[i];
            activity_id = activity["id"];
            value = activity[puField];
            action = activity[puActionField];
            actionvalue = value;
            if (actionvalue > 3) {
                actionvalue = 3;
            }
            puValue = puValue + parseInt(value);
            if (parseInt(action) > 0) {
                puActionA[action] += parseInt(actionvalue);
            }
            console.log(activity["id"]+" "+activity[puField]+":"+activity[puDescField]+";"+activity[puActionField]+":"+activity[puHowField]+" - "+activity[puAutField]);


            strsign = get_mini_pu_value(value, action);

            innerHTML = strsign+" "+activity[puDescField];
            if (activity[puHowField]){
                innerHTML = innerHTML+": "+activity[puHowField];
            }

            puactivity.innerHTML = innerHTML;
            puclass = "pu_activity manual edit";
            activitytext = " Manual - Click to Edit";
            parentdiv = pumanual;
            autgroup = "";

            actionclass = "";
            title = "";
            switch (action){
                case "1": actionclass = " solve";
                    break;
                case "2": actionclass = " circumvent";
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



            autgroup = null;


            if (activity[puAutField] == 1) {
                puclass = "pu_activity automated";
                activitytext = " Automated";
                parentdiv = puautomated;
                //if from smart group, then get group and get manualsolve field value
                puauttype = activity[puAutTypeField];
                if (puauttype &&  puauttype.substring(0,1) == "G") {
                    autgroup = puauttype.substring(1);
          //          puclass = puactivity.getAttribute("class");
                    puclass += " pu_group_"+autgroup;
           //         puactivity.setAttribute("class", puclass);

                 }
            }
            else {

                puactivity.setAttribute("onclick", "create_pu_form('"+activity_id+"', false);");
            }

            title = putext + " - "+activitytext;
            puactivity.setAttribute("title", title);
            puactivity.setAttribute("id", "pu_activity_"+activity_id);
            puactivity.setAttribute("class", puclass+actionclass);

            parentdiv.appendChild(puactivity);
            //  console.log(result.values[i]);
            if (autgroup) {
                CRM.api3('Group', 'get', {
                    "sequential": 1,
                    "id": autgroup,
                    "return": puManualSolvField
                }).done(function (result2) {
                    for (i = 0; i < result2.values.length; i++) {
                        if (result2.values[i][puManualSolvField] == "1") {
                            var activity = document.getElementsByClassName("pu_group_"+result2.values[i]["id"]);

                            puclass = activity[0].getAttribute("class");
                            putitle = activity[0].getAttribute("title");
                            puidtext = activity[0].getAttribute("id");
                            puid = puidtext.substring(12);

                            puclass += " edit";
                            putitle += " - Click to Close";

                            puclass = activity[0].setAttribute("class", puclass);
                            puclass = activity[0].setAttribute("title", putitle);
                            activity[0].setAttribute("onclick", "create_pu_form('" + puid + "',true);");
                        }
                    }

                });
            }

        }



        puhtml.appendChild(pumanual);
        puhtml.appendChild(puautomated);
        puhtml.style.display = "none";
        pufield = document.getElementById("pufield");
        pufield.appendChild(puhtml);

        puActionMax = 0;
        puActionMaxValue = 0;
        for (i = 1; i <= 3; i++){
            if (puActionA[i] > puActionMaxValue){
                puActionMax = i;
                puActionMaxValue = puActionA[i];
            }
        }




        set_pu_value(puValue, puActionMax);

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
            putitle = "circumvent";
            break;
        case "3":
            pucolor = "#00bb00";
            putitle = "acknowlegde";
    }
    ret = "<span style='font-size:"+pusize+"px;color:"+pucolor+";' title='"+putitle+"'>"+putext+"</span>";
    return ret;

}

   function set_pu_value(pu, puaction){

      if (!pu) {
	      pusize = "27";
          putext = "&#8857";
          pudeco = "none";
          pucolor="#bbbb00";
        }
 /*       else if (pu == 1) {
          pusize = "100";
          putext = "*";
          pudeco = "";
          pucolor = "#666666";
        }*/
        else {
          if (pu > 10) pu = 10;
          pusize = 17+(pu*10);
          putext = "&#8857;";
          pudeco = "none";
          pucolor="#666666";
          if (puaction == 1) pucolor = "#0000bb";
          else if (puaction == 2) pucolor = "#bb0000";
          else if (puaction == 3) pucolor = "#00bb00";
        }
        cj("#pusign").css("font-size",pusize+"px").css("text-decoration", pudeco).css("color", pucolor).html(putext);
   }


cj(document).ready(function($) {
 cj("#pufield").append(cj(".Pu_fields"));
 cj(".Pu_fields").css("width", "50%").css("float","right").css("top","-30px").css("position", "relative").hide();

    cj("#pu_activities").css("width", "50%").css("float","right").css("top","-30px").css("position", "relative").hide();

 cj("#pufield").hover(function(){
  cj(".Pu_fields").hide();
     cj("#pu_activities").show();
 }, function(){
  if(cj("#pu_activities .crm-inline-edit-form").length==0){
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
