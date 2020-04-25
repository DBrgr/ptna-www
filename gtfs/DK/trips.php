<!DOCTYPE html>
<html lang="dk">

<?php $title="GTFS Analysen"; $inc_lang='../../dk/'; include $inc_lang.'html-head.inc'; ?>

<?php include('../../script/globals.php'); ?>
<?php include('../../script/gtfs.php'); ?>

    <body>
      <div id="wrapper">

<?php include $inc_lang.'header.inc' ?>

        <main id="main" class="results">
            <?php
                $network  = $_GET['network'];
                $route_id = $_GET['route_id'];
                $route_short_name = GetGtfsRouteShortNameFromRouteId( $network, $route_id );
                if ( !$route_short_name ) {
                     $route_short_name = 'not set';
                }
            ?>

            <h2 id="DK"><img src="/img/Denmark32.png" alt="Flag til Danmark" /> GTFS-analyser for <?php if ( $network && $route_id && $route_short_name ) { echo '<a href="routes.php?network=' .urlencode($network) . '">' . htmlspecialchars($network) . '</a> Linie "' . htmlspecialchars($route_short_name) . '"'; } else { echo "Danmark"; } ?></h2>
            <div class="indent">
<?php include $inc_lang.'gtfs-trips-head.inc' ?>

                <form class="ptna-data" action="trips.php?network=<?php echo urlencode($network);?>&route_id=<?php echo urlencode($route_id);?>" method="post">

                <table id="gtfs-trips">
                    <thead>
<?php include $inc_lang.'gtfs-trips-trth.inc' ?>
                    </thead>
                    <tbody>
<?php $duration = CreateGtfsTripsEntry( $network, $route_id, $route_short_name ); ?>
                    </tbody>
                </table>

                </form>

                <?php printf( "<p>SQL-forespørgsler tog %f sekunder</p>\n", $duration ); ?>

            </div>

        </main> <!-- main -->

        <hr />

<?php include $inc_lang.'gtfs-footer.inc' ?>

      </div> <!-- wrapper -->
    </body>
</html>
