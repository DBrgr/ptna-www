<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>PTNA - Results</title>
        <meta name="generator" content="PTNA" />
        <meta name="keywords" content="OSM Public Transport PTv2" />
        <meta name="description" content="PTNA - Results of the Analysis of various Networks" />
        <meta name="robots" content="noindex,nofollow" />
        <link rel="stylesheet" href="/css/main.css" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="icon" type="image/png" href="/favicon.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="/favicon.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" sizes="any" />
        <?php include('../script/config.php'); ?>
    </head>
    <body>
      <?php if ( isset($_GET['network']) ) { $found = ReadDetails( $_GET['network'] ); } ?>
        
      <div id="wrapper">
        <header id="headerblock">
            <div id="headerimg" class="logo">
                <a href="/"><img src="/img/logo.png" alt="logo" /></a>
            </div>
            <div id="headertext">
                <h1><a href="/">PTNA - Public Transport Network Analysis</a></h1>
                <h2>Static Analysis for OpenStreetMap</h2>
            </div>
            <div id="headernav">
                <a href="/">Home</a> |
                <a href="/contact.html">Contact</a> |
                <a target="_blank" href="https://www.openstreetmap.de/impressum.html">Impressum</a> |
                <a target="_blank" href="https://www.fossgis.de/datenschutzerklärung">Datenschutzerklärung</a> |
                <a href="/en/index.html" title="english"><img src="/img/GreatBritain16.png" alt="Union Jack" /></a>
                <a href="/de/index.html" title="deutsch"><img src="/img/Germany16.png" alt="deutsche Flagge" /></a>
                <!-- <a href="/fr/index.html" title="français"><img src="/img/France16.png" alt="Tricolore Française" /></a> -->
            </div>
        </header>

        <nav id="navigation">
            <h2 id="de">Configuration details <?php if ( $found ) { printf( "fpr %s", $_GET['network'] ); } ?></h2>
            <ul>
                <li><a href="#overpass-api">Overpass-API Query</a></li>
                <li><a href="#options">Analysis options</a></li>
             </ul>
        </nav>

        <hr />

        <main id="main" class="results">

            <h2 id="overpass-api">Overpass-API Query</h2>
            <p>
                The <a href="https://wiki.openstreetmap.org/wiki/Overpass_API">Overpass API</a> is used to download the OSM data.
                <a href="/en/index.php#overpass">The query used</a> returns all ways and nodes of the routes (their members with their details) from a defined search area.
                The data thus obtained allow an analysis of the public transport lines to the effect that e.g. the route can also be checked for completeness.
                Nodes, ways and relations (stops and platforms) and their tags can be checked against their 'role' in the relation.
            </p>
            
            <?php if ( $found ) { 
                      printf( "<p><code>%s</code></p>\n", htmlentities(GetOverpassQuery()) );
                      printf( "<p>This query currently delivers approximately %.1f MB.\n</p>", GetOsmXmlFileSize() );
                      printf( "<p>Show the search area on the <a href=\"%s\">OSM map</a>.</p>\n", GetRegionLink() );
                  }
            ?>

            <hr />

            <h2 id="options">Analysis options</h2>

            <p>
                The output of errors and comments can be influenced by a variety of analysis options. <br />
                Here is a <a href="/en/index.php#messages">list of the error messages and comments</a>.<br />
            </p>

            <table id="message-table">
                <thead>
                    <tr class="message-tableheaderrow">
                        <th class="message-text">Option</th>
                        <th class="message-option">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( $found ) { PrintOptionDetails( "en" ); } ?>
                </tbody>
            </table>

        </main> <!-- main -->

        <hr />

        <footer id="footer">
            <p>
                All geographic data <a href="https://www.openstreetmap.org/copyright">© OpenStreetMap contributors</a>.
            </p>
            <p>
                This program is free software: you can redistribute it and/or modify it under the terms of the <a href="https://www.gnu.org/licenses/gpl.html">GNU GENERAL PUBLIC LICENSE, Version 3, 29 June 2007</a> as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. Get the source code via <a href="https://github.com/osm-ToniE">GitHub</a>.
            </p>
        </footer>

      </div> <!-- wrapper -->
    </body>
</html>
