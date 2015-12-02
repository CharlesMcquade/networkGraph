<!DOCTYPE html>
<meta charset="utf-8">
<style>

.link {
  stroke: #000;
  stroke-width: 1.5px;
}

.node {
  stroke: #fff;
  stroke-width: 1.5px;
}

d3-tip {
    line-height: 1;
    color: black;
}

</style>
<body>
<script src="js/d3.min.js"></script>
<script src="js/d3.tooltip.js"></script>
<script>

//DIMENSIONS, radius for border
var width = 1500,
    height = 750,
    radius = 10;

//how we color the graph. color() is fed
//an index into an 'array' of colors,
//used to group nodes
var color = d3.scale.category10();

var nodes = [],
    links = [];

//force graph, click-and-draggable
var force = d3.layout.force()
    .nodes(nodes)
    .links(links)
    .gravity(.15)
    .charge(-400)
    .linkDistance(50)
    .size([width, height])
    .on("tick", tick);

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height);

//node/edge group declarations
var node = svg.selectAll(".node")
var link = svg.selectAll(".link");

//////////////BEGIN TOOLTIP ON MOUSEOVER CODE
//offset -> adjust text offset
var tip = d3.tip()
    .attr('class', 'd3-tip')
    .offset([-10, 0])
    .html(function (d) {
        console.log(d.id);
        return d.id + "";
    })
    svg.call(tip);
//////////////END TOOLTIP ON MOUSEOVER CODE

// 1. Add three nodes and three links.
<?php
include_once "includes/nodeadder.php";
//print all node/edge declarations and pushes to array
printGraphDeclarations();
?>
start();

//////////////DOUBLE CLICK TO HIGHLIGHT CAPABILITY
//toggle stores whether the highlighting is one
var toggle = 0;
//Create an array logging what is connected to what
var linkedByIndex = {};

/* Prolly not needed
for(i = 0; i < node.length; i++) {
    linkedByIndex[i + "," + i] = 1;
};
 */

//console.log("links.length = " + links.length);
for(i = 0; i < links.length; i++) {
    linkedByIndex[links[i].source.index + "," + links[i].target.index] = 1;
};
//console.log("links.length = " + links.length);

//console.log(linkedByIndex.toString());


//This function looks up whether a pair are neighbors
function neighboring(a, b) {
    return linkedByIndex[a.index + "," + b.index];
}

//Called to fade out all nodes that aren't
//connected to the double clicked node
function connectedNodes() {
    if (toggle == 0) {
        //Reduce opacity of all but the neighboring nodes
        d = d3.select(this).node().__data__;
        node.style("opacity", function(o) {
            //console.log("Selected node: o=" + o.index + ", d=" + d.index +"\n");
            if (d == o) return 1;
            return neighboring(d, o) | neighboring(o, d) ? 1 : 0.1;
        });
        link.style("opacity", function(o) {
            return d.index==o.source.index | d.index==o.target.index ? 1 : 0.1;
        });

    //Reduce the op
    toggle = 1;
    } else {
        //put them back to opacity=1
        node.style("opacity", 1);
        link.style("opacity", 1);
        toggle = 0;
    }
}
//////////////END DOUBLE CLICK TO HIGHLIGHT CODE




function start() {
  link = link.data(force.links(), function(d) { return d.source.id + "-" + d.target.id; });
  link.enter()
      .insert("line", ".node")
      .attr("class", "link");
  link.exit().remove();

  node = node.data(force.nodes(), function(d) { return d.id;});

  //functions for each node on init of graph
  node.enter()
      .append("circle")
      .attr("class", function(d) { return "node " + d.id; })
      .attr("r", 8)
      .style("fill", function(d) { return color(d.group); })
      .call(force.drag)
      .on("dblclick", connectedNodes)
      .on("mouseover", tip.show)
      .on("mouseout", tip.hide);
  node.exit().remove();

  force.start();
}

function tick() {
  //updates position & the radius/math.min is to keep inside the screen
  node.attr("cx", function(d) { return d.x = Math.max(radius, Math.min(width - radius, d.x)); })
      .attr("cy", function(d) { return d.y = Math.max(radius, Math.min(height - radius, d.y)); })

  link.attr("x1", function(d) { return d.source.x; })
      .attr("y1", function(d) { return d.source.y; })
      .attr("x2", function(d) { return d.target.x; })
      .attr("y2", function(d) { return d.target.y; });
}
</script>
