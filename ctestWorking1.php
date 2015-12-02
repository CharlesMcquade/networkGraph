<!DOCTYPE html>
<meta charset="utf-8">
<style>

.link {
  stroke: #000;
  stroke-width: 1.5px;
}

.node {
  fill: #000;
  stroke: #fff;
  stroke-width: 1.5px;
}

.node.a { fill: #1f77b4; }
.node.b { fill: #ff7f0e; }
.node.c { fill: #2ca02c; }

</style>
<body>
<script src="js/d3.min.js"></script>
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
    .charge(-200)
    .linkDistance(50)
    .size([width, height])
    .on("tick", tick);

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height);

var node = svg.selectAll(".node")



var link = svg.selectAll(".link");

// 1. Add three nodes and three links.
setTimeout(function() {
    <?php
    include_once "includes/nodeadder.php";
    printGraphDeclarations();
    ?>
    start();
}, 0);


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
      .call(force.drag);
  node.exit().remove();

  force.start();
}

function tick() {
  node.attr("cx", function(d) { return d.x = Math.max(radius, Math.min(width - radius, d.x)); })
      .attr("cy", function(d) { return d.y = Math.max(radius, Math.min(height - radius, d.y)); })

  link.attr("x1", function(d) { return d.source.x; })
      .attr("y1", function(d) { return d.source.y; })
      .attr("x2", function(d) { return d.target.x; })
      .attr("y2", function(d) { return d.target.y; });
}
</script>
