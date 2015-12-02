<!DOCTYPE html5>
<meta charset="utf-8">
<head>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/d3.min.js"></script>
<script src="js/d3.tooltip.js"></script>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/simple-sidebar.css" rel="stylesheet">
    <!-- Graph CSS -->
    <link href="css/graph-style.css" rel="stylesheet">
</head>
<body>
<div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="#">
                        Start Bootstrap
                    </a>
                </li>
                <li>
                    <a href="#">Dashboard</a>
                </li>
                <li>
                    <a href="#">Shortcuts</a>
                </li>
                <li>
                    <a href="#">Overview</a>
                </li>
                <li>
                    <a href="#">Events</a>
                </li>
                <li>
                    <a href="#">About</a>
                </li>
                <li>
                    <a href="#">Services</a>
                </li>
                <li>
                    <a href="#">Contact</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
                    </div>
                </div>
                <div id="chart" class="row">
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

        <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>
    <!-- graph script -->

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

var svg = d3.select("#chart").append("svg:svg")
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
for(i = 0; i < links.length; i++) {
    linkedByIndex[links[i].source.index + "," + links[i].target.index] = 1;
};



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
            //Checks if a node is neighboring and maintains opacity of this node if so
            if ((d == o) || (neighboring(d, o) | neighboring(o, d))) {
                return 1;
            } else return 0.1; //otherwise, fades the node out
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
