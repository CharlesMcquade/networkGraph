<?php ###################PHP FUNCTIONS #########################

#printGraphDeclarations();


function printGraphDeclarations($filename = "/var/www/html/d3test/dat/large_data_set.txt") {
    global $srcNodes, $destNodes, $edges;
    if (!file_exists($filename)) print "//Error: No file given!";
    else {
    readDataFile($filename);


    print "//###########BEGIN srcNodes declarations###########//\n";
    addNodes($srcNodes, 0);
    print "//#############END srcNodes declarations###########//\n";



    print "\n\n//###########BEGIN destNodes declarations###########//\n";
    addNodes($destNodes, 1);
    print "//#############END destNodes declarations###########//\n";




    print "\n\n\n\n//###########BEGIN edges declarations###########//\n";
    addEdges($edges);
    print "//#############END edges declarations###########//\n";
    }
}




#######################################################################################
    function readDataFile($filename) {
        global $srcNodes, $destNodes, $edges, $conns;

        $data = file_get_contents($filename);
                $lines = explode("\n", $data);
        $uid = 0;
        foreach ($lines as $line) {
                    $matches = preg_split("/ +/", $line);
            #print_r($matches);
            if (count($matches) <= 1) continue;
            list($src,$srcPort) = explode("/", $matches[10]);
            list($dest,$destPort) = explode("/", $matches[12]);
            $conns[] = array( 'Time' => $matches[0]." ".$matches[1]." ".$matches[2],
                         'Direction' => $matches[5],
                         'Protocol' => $matches[6],
                         'Connection Status' => $matches[8],
                         'Source IP' => $src,
                         'Source Port' => $srcPort,
                         'Destination IP' => $dest,
                         'Destination Port' => $destPort,
                         'Flags' => $matches[14],
                         'Interface' => $matches[17]
                     );
            if (!array_key_exists($src, $srcNodes)) $srcNodes[$src] = $uid++;
            if (!array_key_exists($dest, $destNodes)) $destNodes[$dest] = $uid++;
            $edges[$src][$dest]++;
        }
        #print "<pre>";
        #print_r($srcNodes);
        #print_r($destNodes);
        #print_r($edges);
        #print_r($conns);
        #print "</pre>";
    }
    #######################################################################################

    #######################################################################################
    function addNodes($nodes, $group) {
        $out = "";

        foreach ($nodes as $key=>$val) {
            $out .= "\tvar node$val = {id: \"$key\",\"group\":$group}; nodes.push(node$val);\n";
        }
        print $out;
    }
    #######################################################################################


    #######################################################################################
    function addEdges($edges) {
        global $srcNodes, $destNodes;
        $out = "";

        foreach ($edges as $src=>$dests) {
            foreach ($dests as $dest=>$refCount) {
                $out .= "\tvar link".$srcNodes[$src]."to".$destNodes[$dest]
                    ." = {source: node".$srcNodes[$src].", target: node".$destNodes[$dest]."};";
                $out .= "  links.push(link".$srcNodes[$src]."to".$destNodes[$dest].");\n";
            }
        }
        print $out;
    }
    #######################################################################################
      ################### END PHP FUNCTIONS #########################
?>
