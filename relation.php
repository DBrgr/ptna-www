<!DOCTYPE html>
<?php
    $lang     = ( $_GET['lang']   && is_dir('./'.$_GET['lang']) ) ? $_GET['lang'] : 'en';
    $inc_lang = './' . $lang . '/';
?>
<html lang="<?php echo $lang; ?>">

<?php $title="OSM Relation"; include $inc_lang.'html-head.inc'; ?>

    <body>
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
      <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>

      <script src="/script/relation.js"></script>

      <div id="wrapper">

<?php include $inc_lang.'header.inc' ?>

        <main id="main" class="results">

            <div id="relationmap"></div>
            <div class="relationtables">
                <h3 id ="osm-relation">OSM Relation</h3>
                <span class="attention" style="font-weight: 1000;font-size:1.2em;">BETA: Not all members of the relation are currently shown on the map</span>

                <section class="tabbing" id="tbg_blindtext">
                    <input type="radio" id="tbg_blindtext_0" name="tbg_blindtext" class="hackbox" checked>
                    <div class="tabcontent">
                        <ul class="tabs">
                            <li><label for="tbg_blindtext_0" class="tab_active">Details</label></li>
                            <li><label for="tbg_blindtext_1">Platforms</label></li>
                            <li><label for="tbg_blindtext_2">Stops</label></li>
                            <li><label for="tbg_blindtext_3">Ways</label></li>
                        </ul>
                        <table class="relationtable">
                            <thead class="results-tableheaderrow">
                                <tr>
                                    <th class="results-name">Key</th>
                                    <th class="results-name">Value</th>
                                </tr>
                            </thead>
                            <tbody id="relation-values" class="results-tablerow">
                            </tbody>
                        </table>
                    </div>
                    <input type="radio" id="tbg_blindtext_1" name="tbg_blindtext" class="hackbox">
                    <div class="tabcontent">
                        <ul class="tabs">
                            <li><label for="tbg_blindtext_0">Details</label></li>
                            <li><label for="tbg_blindtext_1" class="tab_active">Platforms</label></li>
                            <li><label for="tbg_blindtext_2">Stops</label></li>
                            <li><label for="tbg_blindtext_3">Ways</label></li>
                        </ul>
                        <table class="relationtable">
                            <thead class="results-tableheaderrow">
                                <tr>
                                    <th class="results-name">Role</th>
                                    <th class="results-name">Type</th>
                                    <th class="results-name">Name / ID</th>
                                </tr>
                            </thead>
                            <tbody id="platform-values" class="results-tablerow">
                            </tbody>
                        </table>
                    </div>
                    <input type="radio" id="tbg_blindtext_2" name="tbg_blindtext" class="hackbox">
                    <div class="tabcontent">
                        <ul class="tabs">
                          <li><label for="tbg_blindtext_0">Details</label></li>
                          <li><label for="tbg_blindtext_1">Platforms</label></li>
                          <li><label for="tbg_blindtext_2" class="tab_active">Stops</label></li>
                          <li><label for="tbg_blindtext_3">Ways</label></li>
                        </ul>
                        <table class="relationtable">
                            <thead class="results-tableheaderrow">
                                <tr>
                                    <th class="results-name">Role</th>
                                    <th class="results-name">Type</th>
                                    <th class="results-name">Name / ID</th>
                                </tr>
                            </thead>
                            <tbody id="stop-values" class="results-tablerow">
                            </tbody>
                        </table>
                    </div>
                    <input type="radio" id="tbg_blindtext_3" name="tbg_blindtext" class="hackbox">
                    <div class="tabcontent">
                        <ul class="tabs">
                          <li><label for="tbg_blindtext_0">Details</label></li>
                          <li><label for="tbg_blindtext_1">Platforms</label></li>
                          <li><label for="tbg_blindtext_2">Stops</label></li>
                          <li><label for="tbg_blindtext_3" class="tab_active">Ways</label></li>
                        </ul>
                        <table class="relationtable">
                            <thead class="results-tableheaderrow">
                                <tr>
                                    <th class="results-name">Role</th>
                                    <th class="results-name">Type</th>
                                    <th class="results-name">Name / ID</th>
                                    <th class="results-name">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id="route-values" class="results-tablerow">
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
            <script>
                showrelation();
            </script>

        </main> <!-- main -->

        <hr class="clearing" />

<?php include $inc_lang.'footer.inc' ?>

      </div> <!-- wrapper -->

    </body>
</html>
