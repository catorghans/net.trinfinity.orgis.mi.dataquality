{crmTitle string="Pu Overview"}

<div class="puoverview">
    <div id="puvalue">
        <strong>Pu Value</strong>
        <a class="reset" href="javascript:valuePie.filterAll();dc.redrawAll();" style="display: none;">reset</a>
        <div class="clearfix"></div>
    </div>
    <div id="puaction">
        <strong>Action</strong>
        <a class="reset" href="javascript:actionPie.filterAll();dc.redrawAll();" style="display: none;">reset</a>
        <div class="clearfix"></div>
    </div>
    <div class="clear"></div>
</div>
<script>

    'use strict';

    //console.log({$id});

    var puDetails  = {crmAPI entity="pu_contact" action="getoverview"};

   {literal}

        if((!puDetails.is_error)){
           var valuePie, actionPie;
            cj(function($) {

                function print_filter(filter){var f=eval(filter);if(typeof(f.length)!="undefined"){}else{}if(typeof(f.top)!="undefined"){f=f.top(Infinity);}else{}if(typeof(f.dimension)!="undefined"){f=f.dimension(function(d){return "";}).top(Infinity);}else{}console.log(filter+"("+f.length+")="+JSON.stringify(f).replace("[", "[\n\t").replace(/}\,/g, "},\n\t").replace("]", "\n]"));}

                var ndx = crossfilter(puDetails.values), all = ndx.groupAll();
                var grouped=ndx.groupAll().reduce(function(p,v){ ++p.count; return p; }, function(p,v){p.count-=1;return p;}, function(){return {count:0};});
                valuePie           = dc.pieChart("#puvalue").radius(100);
                actionPie           = dc.pieChart("#puaction").radius(100);

                var value      = ndx.dimension(function(d){return d.name});
                var valueGroup = value.group().reduceCount();

                var action      = ndx.dimension(function(d){return d.actionname});
                var actionGroup = action.group().reduceCount();

                valuePie
                    .width(220)
                    .height(220)
                    .dimension(value)
                    .group(valueGroup);


                actionPie
                    .width(220)
                    .height(220)
                    .dimension(action)
                    .group(actionGroup);
         
                dc.renderAll();
           });

        }

   {/literal}
</script>

