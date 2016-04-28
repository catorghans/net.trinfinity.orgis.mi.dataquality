{crmTitle string="Pu Overview"}

<div class="puoverview">
    <div id="puvalue">
        <strong>Pu Value</strong>
        <a class="reset" href="javascript:valuePie.filterAll();dc.redrawAll();" style="display: none;">reset</a>
        <div class="clearfix"></div>
    </div>
    <div id="puaction">
        <strong>Pu Action</strong>
        <a class="reset" href="javascript:actionPie.filterAll();dc.redrawAll();" style="display: none;">reset</a>
        <div class="clearfix"></div>
    </div>
    <div class="clear"></div>
    <table id="puContactsTable">
        <thead>
            <tr class="header">
                <th>id</th>
                <th>Name</th>
                <th>Pu Value</th>
                <th>Pu Type</th>
            </tr>
        </thead>
    </table>


</div>
<script>

    'use strict';

    //console.log({$id});

    var puDetails  = {crmAPI entity="pu_contact" action="getoverview"};

    const PUCOLORS = [ "#000000", "#0000bb", "#bb0000", "#00bb00" ]; 
                 // undefined, "Acknowledge", "Circumvent", "Solve"

   {literal}

        if((!puDetails.is_error)){
           var valuePie, actionPie,dataTable;
            cj(function($) {

                var puColorScale = d3.scale.ordinal()
                  .domain(Object.keys(PUCOLORS))
                  .range(PUCOLORS);

                function print_filter(filter){var f=eval(filter);if(typeof(f.length)!="undefined"){}else{}if(typeof(f.top)!="undefined"){f=f.top(Infinity);}else{}if(typeof(f.dimension)!="undefined"){f=f.dimension(function(d){return "";}).top(Infinity);}else{}console.log(filter+"("+f.length+")="+JSON.stringify(f).replace("[", "[\n\t").replace(/}\,/g, "},\n\t").replace("]", "\n]"));}

                var ndx = crossfilter(puDetails.values);

                valuePie           = dc.pieChart("#puvalue").radius(100);
                actionPie           = dc.pieChart("#puaction").radius(100);
                dataTable           = dc.dataTable("#puContactsTable");

                var value      = ndx.dimension(function(d){return d.name});
                var valueGroup = value.group().reduceCount();

                var actionNr    = ndx.dimension(function(d){return d.action});
                var actionGroup = actionNr.group()
                  .reduce(
                    function(p,v){ ++p.count; p.name = v.actionname; return p; }, 
                    function(p,v){p.count-=1;return p;}, 
                    function(){return {count:0, name:''};}
                   );

                var id        = ndx.dimension(function(d){return d.id;});


                valuePie
                    .width(220)
                    .height(220)
                    .dimension(value)
                    .group(valueGroup);


                actionPie
                    .width(220)
                    .height(220)
                    .colors(puColorScale)
                    .dimension(actionNr)
                    .group(actionGroup)
                    .colorAccessor( function (p) { return p.key; })
                    .keyAccessor( function (p) { return p.key; })
                    .valueAccessor( function (p) { return p.value.count; })
                    .label( function (p) { return p.value.name; })
                    .title( function (p) { return p.value.name +" : "+ p.value.count; });

               dataTable
                    .dimension(id)
                    .group(function(d){ return ""; })
                    .size(25)
                    .columns(
                        [
                            function (d) {
                                return d.id;
                            },
                            function (d) {
                                return "<a href='/civicrm/contact/view?reset=1&cid="+d.id+"'>"+d.display_name+"</a>";
                            },
                            function (d) {
                                return d.name;
                            },
                            function (d) {
                                return d.actionname;
                            }
                        ]
                    );

         
                dc.renderAll();
           });

        }

   {/literal}
</script>

