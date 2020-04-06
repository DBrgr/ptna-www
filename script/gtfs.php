<?php

    function FindGtfsSqliteDb( $network ) {
        global $path_to_work;
        
        $return_path = '';

        if ( $network && preg_match("/^[a-zA-Z0-9_.-]+$/", $network) ) {
            $prefixparts = explode( '-', $network );
            $countrydir  = array_shift( $prefixparts );
            
            $return_path = $path_to_work . $countrydir . '/' . $network . '-ptna-gtfs-sqlite.db';

            if ( file_exists($return_path) ) {
                return $return_path;
            } else {
                $subdir = array_shift( $prefixparts );

                $return_path = $path_to_work . $countrydir . '/' . $subdir . '/' . $network . '-ptna-gtfs-sqlite.db';
            
                if ( file_exists($return_path) ) {
                    return $return_path;
                }
            }
        
        }
        
        return '';
    }


    function CreateGtfsEntry( $network ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

            try {

                $start_time = gettimeofday(true);
                
                $db         = new SQLite3( $SqliteDb );
                
                $sql        = "SELECT * FROM ptna";
                
                $ptna       = $db->querySingle( $sql, true );
                
                $sql        = "SELECT * FROM feed_info";
                
                $feed       = $db->querySingle( $sql, true );
                
                echo '                        <tr class="gtfs-tablerow">' . "\n";
                echo '                            <td class="gtfs-name"><a href="routes.php?network=' . urlencode($network) . '">' . htmlspecialchars($network) . '</a></td>' . "\n";
                if ( $ptna["network_name"] ) {
                    if ( $ptna["network_name_url"] ) {
                        echo '                            <td class="gtfs-text"><a target="_blank" href="' . $ptna["network_name_url"] . '">' . htmlspecialchars($ptna["network_name"]) . '</a></td>' . "\n";
                    } else {
                        echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["network_name"]) . '</td>' . "\n";
                    }
                } else {
                    echo '                            <td class="gtfs-text">&nbsp;</td>' . "\n";
                }
                if ( $feed["feed_publisher_name"] ) {
                    if ( $feed["feed_publisher_url"] ) {
                        echo '                            <td class="gtfs-text"><a target="_blank" href="' . $feed["feed_publisher_url"] . '" title="From GTFS">' . htmlspecialchars($feed["feed_publisher_name"]) . '</a></td>' . "\n";
                    } else {
                        echo '                            <td class="gtfs-text">' . htmlspecialchars($feed["feed_publisher_name"]) . '</td>' . "\n";
                    }
                } else {
                    if ( $ptna["feed_publisher_url"] ) {
                        echo '                            <td class="gtfs-text"><a target="_blank" href="' . $ptna["feed_publisher_url"] . '" title="From PTNA">' . htmlspecialchars($ptna["feed_publisher_name"]) . '</a></td>' . "\n";
                    } else {
                        echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["feed_publisher_name"]) . '</td>' . "\n";
                    }
                }
                if ( $feed["feed_start_date"] ) {
                    if ( preg_match( "/^(\d{4})(\d{2})(\d{2})$/", $feed["feed_start_date"], $parts ) ) {
                        echo '                            <td class="gtfs-date">' . $parts[1] . '-' .  $parts[2] . '-' .  $parts[3] . '</td>' . "\n";
                    } else {
                        echo '                            <td class="gtfs-date">' . htmlspecialchars($feed["feed_start_date"]) . '</td>' . "\n";
                    }
                } else {
                    echo '                            <td class="' . $class . '">&nbsp;</td>' . "\n";
                }
                if ( $feed["feed_end_date"] ) {
                    if ( preg_match( "/^(\d{4})(\d{2})(\d{2})$/", $feed["feed_end_date"], $parts ) ) {
                        $class = "gtfs-date";
                        $timestampToday        = time();
                        $end_day               = new DateTime( $feed["feed_end_date"] );
                        $timestampEndDate      = $end_day->format( 'U' );
                        if ( $timestampEndDate < $timestampToday )
                        {
                            $class = "gtfs-dateold";
                        }
                        echo '                            <td class="' . $class . '">' . $parts[1] . '-' .  $parts[2] . '-' .  $parts[3] . '</td>' . "\n";
                    } else {
                        echo '                            <td class="' . $class . '">' . htmlspecialchars($feed["feed_end_date"]) . '</td>' . "\n";
                    }
                } else {
                    echo '                            <td class="' . $class . '">&nbsp;</td>' . "\n";
                }
                echo '                            <td class="gtfs-number">' . htmlspecialchars($feed["feed_version"]) . '</td>' . "\n";
                if ( $ptna["release_date"] ) {
                    $tdclass    = "gtfs-date";
                    $txclasstag = "";
                    if ( preg_match( "/^(\d{4})-(\d{2})-(\d{2})$/", $ptna["release_date"], $parts ) ) {
                        $timestampTwoDaysAgo   = time() - ( 2 * 24 * 3600);
                        $timestampFiveDaysAgo  = time() - ( 5 * 24 * 3600);
                        $timestampTenDaysAgo   = time() - (10 * 24 * 3600);
                        $release_day           = new DateTime( $ptna["release_date"] );
                        $timestampReleaseDate  = $release_day->format( 'U' );
                        if ( $timestampReleaseDate >= $timestampTenDaysAgo )
                        {
                            $tdclass = "gtfs-datenew";
                            if ( $timestampReleaseDate >= $timestampFiveDaysAgo )
                            {
                                $tdclass = "gtfs-datenewer";
                                if ( $timestampReleaseDate >= $timestampTwoDaysAgo )
                                {
                                    $tdclass    = "gtfs-dateverynew";
                                    $txclasstag = 'class="gtfs-whitetext"';
                                }
                            }
                        }
                    }
                    if ( $ptna["release_url"] ) {
                        echo '                            <td class="' . $tdclass . '"><a target="_blank" href="' . $ptna["release_url"] . '"><span ' . $txclasstag . '">' . htmlspecialchars($ptna["release_date"]) . '</span></a></td>' . "\n";
                    } else {
                        echo '                            <td class="' . $tdclass . '"><span ' . $txclasstag . '">' . htmlspecialchars($ptna["release_date"]) . '</span></td>' . "\n";
                    }
                } else {
                    echo '                            <td class="gtfs-date">&nbsp;</td>' . "\n";
                }
                echo '                            <td class="gtfs-text"><a href="/en/gtfs-details.php?network=' . urlencode($network) . '">Details, ...</a></td>' . "\n";
                echo '                        </tr>' . "\n";
                
                $db->close();
    
                $stop_time = gettimeofday(true);
                
                return $stop_time - $start_time;
    
            } catch ( Exception $ex ) {
                echo '                        <tr class="gtfs-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">' . htmlspecialchars($network) . '</a></td>' . "\n";
                echo '                            <td class="gtfs-comment" colspan=7>SQLite DB: error opening data base</td>' . "\n";
                echo '                        </tr>' . "\n";
            }
        } else {
            echo '                        <tr class="gtfs-tablerow">' . "\n";
            echo '                            <td class="gtfs-name">' . htmlspecialchars($network) . '</a></td>' . "\n";
            echo '                            <td class="gtfs-comment" colspan=7>SQLite DB: data base not found (data not yet available?)</td>' . "\n";
            echo '                        </tr>' . "\n";
        }
        
        return 0;
    }

    function CreateGtfsRoutesEntry( $network ) {

        ob_implicit_flush(true);
        
        $route_type_to_string["0"]   = 'Tram, Streetcar, Light rail';
        $route_type_to_string["1"]   = 'Subway, Metro';
        $route_type_to_string["2"]   = 'Rail';
        $route_type_to_string["3"]   = 'Bus';
        $route_type_to_string["4"]   = 'Ferry';
        $route_type_to_string["5"]   = 'Cable tram';
        $route_type_to_string["6"]   = 'Aerialway';
        $route_type_to_string["7"]   = 'Funicular';
        $route_type_to_string["11"]  = 'Trolleybus';
        $route_type_to_string["12"]  = 'Monorail';

        # https://developers.google.com/transit/gtfs/reference/extended-route-types

        #                      Code 	Description 	                  Support 	   Examples
        $route_type_to_string["100"] = 'Railway Service';                 # Yes 	 
        $route_type_to_string["101"] = 'High Speed Rail Service';               # Yes 	TGV (FR), ICE (DE), Eurostar (GB)
        $route_type_to_string["102"] = 'Long Distance Trains';            	      # Yes 	InterCity/EuroCity
        $route_type_to_string["103"] = 'Inter Regional Rail Service';               # Yes 	InterRegio (DE), Cross County Rail (GB)
        $route_type_to_string["104"] = 'Car Transport Rail Service';            	 	  	 
        $route_type_to_string["105"] = 'Sleeper Rail Service';            	     # Yes 	GNER Sleeper (GB)
        $route_type_to_string["106"] = 'Regional Rail Service';                 # Yes 	TER (FR), Regionalzug (DE)
        $route_type_to_string["107"] = 'Tourist Railway Service';            	      # Yes 	Romney, Hythe & Dymchurch (GB)
        $route_type_to_string["108"] = 'Rail Shuttle (Within Complex)';                 # Yes 	Gatwick Shuttle (GB), Sky Line (DE)
        $route_type_to_string["109"] = 'Suburban Railway';            	     # Yes 	S-Bahn (DE), RER (FR), S-tog (Kopenhagen)
        $route_type_to_string["110"] = 'Replacement Rail Service';            	 	  	 
        $route_type_to_string["111"] = 'Special Rail Service';            		  	 
        $route_type_to_string["112"] = 'Lorry Transport Rail Service';            		  	 
        $route_type_to_string["113"] = 'All Rail Services';            	 	  	 
        $route_type_to_string["114"] = 'Cross-Country Rail Service';            	 	  	 
        $route_type_to_string["115"] = 'Vehicle Transport Rail Service';            	 	  	 
        $route_type_to_string["116"] = 'Rack and Pinion Railway';            	      # Rochers de Naye (CH), Dolderbahn (CH)
        $route_type_to_string["117"] = 'Additional Rail Service';            	 	  	 
        $route_type_to_string["200"] = 'Coach Service 	Yes';            	 	 
        $route_type_to_string["201"] = 'International Coach Service';            	     # Yes 	EuroLine, Touring
        $route_type_to_string["202"] = 'National Coach Service';            	      # Yes 	National Express (GB)
        $route_type_to_string["203"] = 'Shuttle Coach Service';            	 	      # Roissy Bus (FR), Reading-Heathrow (GB)
        $route_type_to_string["204"] = 'Regional Coach Service';            	     # Yes 	 
        $route_type_to_string["205"] = 'Special Coach Service';            	 	  	 
        $route_type_to_string["206"] = 'Sightseeing Coach Service';            	 	  	 
        $route_type_to_string["207"] = 'Tourist Coach Service';            	 	  	 
        $route_type_to_string["208"] = 'Commuter Coach Service';            	 	  	 
        $route_type_to_string["209"] = 'All Coach Services';            	 	  	 
        $route_type_to_string["400"] = 'Urban Railway Service';            	      # 	Yes 	 
        $route_type_to_string["401"] = 'Metro Service';                # Yes 	Métro de Paris
        $route_type_to_string["402"] = 'Underground Service';                # Yes 	London Underground, U-Bahn
        $route_type_to_string["403"] = 'Urban Railway Service';                # Yes 	 
        $route_type_to_string["404"] = 'All Urban Railway Services'; 	  	 
        $route_type_to_string["405"] = 'Monorail';                # Yes 	 
        $route_type_to_string["700"] = 'Bus Service';                # Yes 	 
        $route_type_to_string["701"] = 'Regional Bus Service';                # Yes 	Eastbourne-Maidstone (GB)
        $route_type_to_string["702"] = 'Express Bus Service';                # Yes 	X19 Wokingham-Heathrow (GB)
        $route_type_to_string["703"] = 'Stopping Bus Service'; 	  	     # 38 London: Clapton Pond-Victoria (GB)
        $route_type_to_string["704"] = 'Local Bus Service';                # Yes 	 
        $route_type_to_string["705"] = 'Night Bus Service'; 	      # N prefixed buses in London (GB)
        $route_type_to_string["706"] = 'Post Bus Service'; 	       # Maidstone P4 (GB)
        $route_type_to_string["707"] = 'Special Needs Bus'; 	  	 
        $route_type_to_string["708"] = 'Mobility Bus Service'; 	  	 
        $route_type_to_string["709"] = 'Mobility Bus for Registered Disabled'; 	  	 
        $route_type_to_string["710"] = 'Sightseeing Bus'; 	  	 
        $route_type_to_string["711"] = 'Shuttle Bus'; 	  	     # 747 Heathrow-Gatwick Airport Service (GB)
        $route_type_to_string["712"] = 'School Bus'; 	  	 
        $route_type_to_string["713"] = 'School and Public Service Bus'; 	  	 
        $route_type_to_string["714"] = 'Rail Replacement Bus Service'; 	  	 
        $route_type_to_string["715"] = 'Demand and Response Bus Service';                # Yes 	 
        $route_type_to_string["716"] = 'All Bus Services'; 	  	 
        $route_type_to_string["800"] = 'Trolleybus Service';                # Yes 	 
        $route_type_to_string["900"] = 'Tram Service';                # Yes 	 
        $route_type_to_string["901"] = 'City Tram Service'; 	  	 
        $route_type_to_string["902"] = 'Local Tram Service'; 	      # Munich (DE), Brussels (BE), Croydon (GB)
        $route_type_to_string["903"] = 'Regional Tram Service'; 	  	 
        $route_type_to_string["904"] = 'Sightseeing Tram Service'; 	      # Blackpool Seafront (GB)
        $route_type_to_string["905"] = 'Shuttle Tram Service'; 	  	 
        $route_type_to_string["906"] = 'All Tram Services'; 	  	 
        $route_type_to_string["1000"] = 'Water Transport Service';                # Yes 	 
        $route_type_to_string["1100"] = 'Air Service'; 	  	 
        $route_type_to_string["1200"] = 'Ferry Service';                    # Yes 	 
        $route_type_to_string["1300"] = 'Aerial Lift Service';              # Yes 	Telefèric de Montjuïc (ES), Saleve (CH), Roosevelt Island Tramway (US)
        $route_type_to_string["1400"] = 'Funicular Service';                # Yes 	Rigiblick (Zürich, CH)
        $route_type_to_string["1500"] = 'Taxi Service'; 	  	 
        $route_type_to_string["1501"] = 'Communal Taxi Service';            # Yes 	Marshrutka (RU), dolmuş (TR)
        $route_type_to_string["1502"] = 'Water Taxi Service'; 	  	 
        $route_type_to_string["1503"] = 'Rail Taxi Service'; 	  	 
        $route_type_to_string["1504"] = 'Bike Taxi Service'; 	  	 
        $route_type_to_string["1505"] = 'Licensed Taxi Service'; 	  	 
        $route_type_to_string["1506"] = 'Private Hire Service Vehicle'; 	  	 
        $route_type_to_string["1507"] = 'All Taxi Services'; 	  	 
        $route_type_to_string["1700"] = 'Miscellaneous Service';            # Yes 	 
        $route_type_to_string["1702"] = 'Horse-drawn Carriage';             # Yes 	                     

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

           if (  $network ) {
                
               try {
                   
                    $today      = new DateTime();
                        
                    $start_time = gettimeofday(true);
                    
                    $db         = new SQLite3( $SqliteDb );
                    
                    $sql        = "SELECT DISTINCT    *
                                   FROM               routes 
                                   JOIN               agency ON routes.agency_id = agency.agency_id 
                                   ORDER BY CASE WHEN route_short_name GLOB '[^0-9]*' THEN route_short_name ELSE CAST(route_short_name AS INTEGER) END;";
    
                    $outerresult = $db->query( $sql );
                    
                    while ( $outerrow=$outerresult->fetchArray() ) {

                        if ( $outerrow["route_type"] && $route_type_to_string[$outerrow["route_type"]] ) {
                            $route_type_text = $route_type_to_string[$outerrow["route_type"]];
                        } else {
                            $route_type_text = $outerrow["route_type"];
                        }
                                
                        $sql = sprintf( "SELECT DISTINCT calendar.start_date,calendar.end_date 
                                         FROM            trips 
                                         JOIN            calendar ON trips.service_id = calendar.service_id 
                                         WHERE           trip_id  IN 
                                                         (SELECT   trips.trip_id
                                                          FROM     trips
                                                          WHERE    trips.route_id='%s') AND %s >= calendar.start_date AND %s <= calendar.end_date
                                                          ORDER BY calendar.end_date DESC, calendar.start_date ASC LIMIT 1;", SQLite3::escapeString($outerrow["route_id"]), $today->format('Ymd'), $today->format('Ymd') );
                                                   
                        $innerresult = $db->query( $sql );
                    
                        while ( $innerrow=$innerresult->fetchArray() ) {
    
                            echo '                        <tr class="gtfs-tablerow">' . "\n";
                            if ( $outerrow["route_short_name"] ) {
                                echo '                            <td class="gtfs-name"><a href="trips.php?network=' . urlencode($network) . '&route_id=' . urlencode($outerrow["route_id"]) . '"><span class="route_short_name">' . htmlspecialchars($outerrow["route_short_name"]) . '</span></a></td>' . "\n";
                            } else {
                                echo '                            <td class="gtfs-name"><a href="trips.php?network=' . urlencode($network) . '&route_id=' . urlencode($outerrow["route_id"]) . '"><span class="route_short_name">-unknown-</span></td>' . "\n";
                            }
                            echo '                            <td class="gtfs-text"><span class="route_type">' . htmlspecialchars($route_type_text) . '</span></td>' . "\n";
                            if ( preg_match( "/^(\d{4})(\d{2})(\d{2})$/", $innerrow["start_date"], $parts ) ) {
                                echo '                            <td class="gtfs-date">' . $parts[1] . '-' .  $parts[2] . '-' .  $parts[3] . '</td>' . "\n";
                            } else {
                                echo '                            <td class="gtfs-date">' . htmlspecialchars($innerrow["start_date"]) . '</td>' . "\n";
                            }
                            if ( preg_match( "/^(\d{4})(\d{2})(\d{2})$/", $innerrow["end_date"], $parts ) ) {
                                echo '                            <td class="gtfs-date">' . $parts[1] . '-' .  $parts[2] . '-' .  $parts[3] . '</td>' . "\n";
                            } else {
                                echo '                            <td class="gtfs-date">' . htmlspecialchars($innerrow["end_date"]) . '</td>' . "\n";
                            }
                            if ( $outerrow["route_long_name"] ) {
                                echo '                            <td class="gtfs-text"><span class="route_long_name">' . htmlspecialchars($outerrow["route_long_name"]) . '</span></td>' . "\n";
                            } elseif ( $outerrow["route_desc"] ) {
                                echo '                            <td class="gtfs-text"><span class="route_long_name">' . htmlspecialchars($outerrow["route_desc"]) . '</span></td>' . "\n";
                            } else {
                                echo '                            <td class="gtfs-text"><span class="route_long_name">' . htmlspecialchars($outerrow["route_id"]) . '</span></td>' . "\n";
                            }
                            if ( $outerrow["agency_url"] ) {
                                echo '                            <td class="gtfs-text"><a target="_blank" href="' . $outerrow["agency_url"]. '"><span class="agency_name">' . htmlspecialchars($outerrow["agency_name"]) . '</span></a></td>' . "\n";
                            } else {
                                echo '                            <td class="gtfs-text"><span class="agency_name">' . htmlspecialchars($outerrow["agency_name"]) . '</span></td>' . "\n";
                            }
#                            if ( $innerrow["ptna_is_invalid"] ) { $checked = '<img src="/img/CheckMark.svg" width=32 height=32 alt="checked" />'; } else { $checked = ''; }
#                            echo '                            <td class="gtfs-checkbox">' . $checked . '</td>' . "\n";
#                            if ( $innerrow["ptna_is_wrong"]   ) { $checked = '<img src="/img/CheckMark.svg" width=32 height=32 alt="checked" />'; } else { $checked = ''; }
#                            echo '                            <td class="gtfs-checkbox">' . $checked . '</td>' . "\n";
                            echo '                            <td class="gtfs-comment">' . htmlspecialchars($innerrow["ptna_comment"]) . '</td>' . "\n";
                            echo '                        </tr>' . "\n";
                        }
                    }
                    $db->close();
                    
                    $stop_time = gettimeofday(true);
                    
                    return $stop_time - $start_time;
        
                } catch ( Exception $ex ) {
                    echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
                }
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return 0;
    }


    function CreateGtfsTripsEntry( $network, $route_id, $route_short_name ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

           if (  $route_id ) {
                
                try {
                   
                    $start_time = gettimeofday(true);
                    
                    $db         = new SQLite3( $SqliteDb );
                    
                    $sql        = sprintf( "SELECT   trip_id
                                            FROM     trips 
                                            WHERE    route_id='%s'
                                            ORDER BY trip_id;",
                                            SQLite3::escapeString($route_id)
                                         );
                    
                    $outerresult = $db->query( $sql );
                    
                    $trip_array = array ();
                    
                    while ( $outerrow=$outerresult->fetchArray() ) {
    
                        $sql = sprintf( "SELECT   GROUP_CONCAT(stop_times.stop_id,'|') AS stop_id_list, GROUP_CONCAT(stops.stop_name,'  |') AS stop_name_list 
                                         FROM     stops
                                         JOIN     stop_times on stop_times.stop_id = stops.stop_id 
                                         WHERE    stop_times.trip_id='%s' 
                                         ORDER BY CAST (stop_times.stop_sequence AS INTEGER);",
                                         SQLite3::escapeString($outerrow["trip_id"])
                                      );
                                                   
                        $innerrow = $db->querySingle( $sql, true );
                    
                        if ( $innerrow["stop_id_list"] && !isset($stoplist[$innerrow["stop_id_list"]]) ) {
                            $stoplist[$innerrow["stop_id_list"]] = $outerrow["trip_id"];
                            $stop_names = $innerrow["stop_name_list"] . '  |' . $outerrow["trip_id"];
                            array_push( $trip_array, $stop_names );
                        }
                    }
                    
                    sort( $trip_array );

                    foreach ( $trip_array as $stop_names ) {

                        $stop_name_array = explode( '  |', $stop_names );
                        $trip_id         = array_pop($stop_name_array);
                        $first_stop_name = array_shift( $stop_name_array );
                        $last_stop_name  = array_pop(   $stop_name_array );
                        $via_stop_names  = implode( ' => ', $stop_name_array );
                        
                        $sql    = sprintf( "SELECT ptna_is_invalid,ptna_is_wrong,ptna_comment 
                                            FROM   trips
                                            WHERE  trip_id='%s';",
                                            SQLite3::escapeString($trip_id)
                                         );
                    
                        $ptnarow = $db->querySingle( $sql, true );
                    
                        echo '                        <tr class="gtfs-tablerow">' . "\n";
                        if ( $route_short_name ) {
                            echo '                            <td class="gtfs-name">' . htmlspecialchars($route_short_name) . '</td>' . "\n";
                        } else {
                            echo '                            <td class="gtfs-name">__not_set__</td>' . "\n";
                        }
                        echo '                            <td class="gtfs-name"><a href="single-trip.php?network=' . urlencode($network) . '&trip_id=' . urlencode($trip_id) . '">' . htmlspecialchars($trip_id) . '</a></td>' . "\n";
                        echo '                            <td class="gtfs-name">'     . htmlspecialchars($first_stop_name)            . '</td>' . "\n";
                        echo '                            <td class="gtfs-text">'     . htmlspecialchars($via_stop_names)             . '</td>' . "\n";
                        echo '                            <td class="gtfs-name">'     . htmlspecialchars($last_stop_name)             . '</td>' . "\n";
#                        if ( $ptnarow["ptna_is_invalid"] ) { $checked = '<img src="/img/CheckMark.svg" width=32 height=32 alt="checked" />'; } else { $checked = ''; }
#                        echo '                            <td class="gtfs-checkbox">' . $checked . '</td>' . "\n";
#                        if ( $ptnarow["ptna_is_wrong"]   ) { $checked = '<img src="/img/CheckMark.svg" width=32 height=32 alt="checked" />'; } else { $checked = ''; }
#                        echo '                            <td class="gtfs-checkbox">' . $checked . '</td>' . "\n";
                        echo '                            <td class="gtfs-comment">' . htmlspecialchars($ptnarow["ptna_comment"]) . '</td>' . "\n";
                        echo '                        </tr>' . "\n";
                    }
                    
                    $db->close();
                    
                    $stop_time = gettimeofday(true);
                    
                    return $stop_time - $start_time;
        
                } catch ( Exception $ex ) {
                    echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
                }
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return 0;
    }
    

    function CreateGtfsSingleTripEntry( $network, $trip_id, $edit ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '') {

           if ( $trip_id ) {
                
               try {
                   
                    $start_time = gettimeofday(true);
                    
                    $db = new SQLite3( $SqliteDb );
                    
                    $sql = sprintf( "SELECT   stop_times.stop_id,stops.stop_name,stops.stop_lat,stops.stop_lon,stops.ptna_is_invalid,stops.ptna_is_wrong,stops.ptna_comment 
                                     FROM     stop_times
                                     JOIN     stops ON stop_times.stop_id = stops.stop_id
                                     WHERE    stop_times.trip_id='%s' 
                                     ORDER BY CAST (stop_times.stop_sequence AS INTEGER);",
                                     SQLite3::escapeString($trip_id) 
                                  );
            
                    $result = $db->query( $sql );
                    
                    $counter = 1;
                    while ( $row=$result->fetchArray() ) {
                        echo '                       <tr class="gtfs-tablerow">' . "\n";
                        echo '                           <td class="gtfs-number">'   . $counter++ . '</td>' . "\n";
                        echo '                           <td class="gtfs-name">'     . htmlspecialchars($row["stop_name"])       . '</td>' . "\n";
                        echo '                           <td class="gtfs-lat">'      . htmlspecialchars($row["stop_lat"])        . '</td>' . "\n";
                        echo '                           <td class="gtfs-lon">'      . htmlspecialchars($row["stop_lon"])        . '</td>' . "\n";
                        echo '                           <td class="gtfs-id">'       . htmlspecialchars($row["stop_id"])         . '</td>' . "\n";
#                        if ( $row["ptna_is_invalid"] ) { $checked = '<img src="/img/CheckMark.svg" width=32 height=32 alt="checked" />'; } else { $checked = ''; }
#                        echo '                           <td class="gtfs-checkbox">' . $checked . '</td>' . "\n";
#                        if ( $row["ptna_is_wrong"]   ) { $checked = '<img src="/img/CheckMark.svg" width=32 height=32 alt="checked" />'; } else { $checked = ''; }
#                        echo '                           <td class="gtfs-checkbox">' . $checked . '</td>' . "\n";
                        echo '                           <td class="gtfs-comment">'  . htmlspecialchars($row["ptna_comment"]) . '</td>' . "\n";
                        echo '                       </tr>' . "\n";
                    }
                    
                    $db->close();
                    
                    $stop_time = gettimeofday(true);
                    
                    return $stop_time - $start_time;
        
                } catch ( Exception $ex ) {
                    echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
                }
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return 0;
    }
    

    function CreateGtfsSingleTripShapeEntry( $network, $trip_id ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '') {

           if ( $trip_id ) {
                
               try {
                   
                    $start_time = gettimeofday(true);
                    
                    $db = new SQLite3( $SqliteDb );
                        
                    $sql        = "SELECT * FROM ptna";
                
                    $ptna       = $db->querySingle( $sql, true );
                
                    if ( $ptna["has_shapes"] ) {
                        
                        $sql        = sprintf( "SELECT * FROM trips WHERE trip_id='%s'", SQLite3::escapeString($trip_id) );
                    
                        $trip       = $db->querySingle( $sql, true );
                        
                        $shape_id   = $trip["shape_id"];
                        
                        if ( $shape_id ) {
                        
                            $sql = sprintf( "SELECT   * 
                                             FROM     shapes
                                             WHERE    shape_id='%s' 
                                             ORDER BY CAST (shape_pt_sequence AS INTEGER);",
                                             SQLite3::escapeString($shape_id) 
                                          );
                    
                            $result = $db->query( $sql );
                            
                            echo "              <p>\n";
                            echo "                  <strong>GTFS Shape Data</strong>\n";
                            echo "              </p>\n";
                            echo '              <table id="gtfs-shape">' . "\n";
                            echo '                  <thead>' . "\n";
                            echo '                      <tr class="gtfs-tableheaderrow">' . "\n";
                            echo '                          <th class="gtfs-name">Number</th>' . "\n";
                            echo '                          <th class="gtfs-number">Latitude</th>' . "\n";
                            echo '                          <th class="gtfs-number">Longitude</th>' . "\n";
                            echo '                      </tr>' . "\n";
                            echo '                  </thead>' . "\n";
                            echo '                  <tbody>' . "\n";
                            $counter = 1;
                            while ( $row=$result->fetchArray() ) {
                                echo '                      <tr class="gtfs-tablerow">' . "\n";
                                echo '                          <td class="gtfs-number">'  . $counter++ . '</td>' . "\n";
                                echo '                          <td class="gtfs-lat">'     . htmlspecialchars($row["shape_pt_lat"])        . '</td>' . "\n";
                                echo '                          <td class="gtfs-lon">'     . htmlspecialchars($row["shape_pt_lon"])        . '</td>' . "\n";
                                echo '                      </tr>' . "\n";
                            }
                            echo '                  </tbody>' . "\n";
                            echo '              </table>' . "\n";
                        }
                    }
                    $db->close();
                    
                    $stop_time = gettimeofday(true);
                    
                    return $stop_time - $start_time;
        
                } catch ( Exception $ex ) {
                    echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
                }
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return 0;
    }
    

    function GetPtnaComment( $network ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

            try {

                $db  = new SQLite3( $SqliteDb );
                
                $sql        = "SELECT * FROM ptna";
                
                $ptna       = $db->querySingle( $sql, true );
                
                return $ptna["comment"];
                
            } catch ( Exception $ex ) {
                echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return '';
    }
    

    function GetGtfsRouteShortNameFromRouteId( $network, $route_id ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

            if ( $route_id ) {
                
                try {
    
                    $db = new SQLite3( $SqliteDb );
                    
                    $sql = sprintf( "SELECT route_short_name 
                                     FROM   routes 
                                     WHERE  route_id='%s';", 
                                     SQLite3::escapeString($route_id)
                                  );

                    $row = $db->querySingle( $sql, true );
                    
                    return $row["route_short_name"];
                    
                } catch ( Exception $ex ) {
                    echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
                }
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return '';
    }
    

    function GetGtfsRouteShortNameFromTripId( $network, $trip_id ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

            if ( $trip_id ) {
                
                try {
    
                    $db = new SQLite3( $SqliteDb );
                    
                    $sql = sprintf( "SELECT route_short_name 
                                     FROM   routes 
                                     JOIN   trips ON trips.route_id = routes.route_id 
                                     WHERE  trip_id='%s';", 
                                     SQLite3::escapeString($trip_id)
                                  );

                    $row = $db->querySingle( $sql, true );
                    
                    return $row["route_short_name"];
                    
                } catch ( Exception $ex ) {
                    echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
                }
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return '';
    }
    

    function GetPtnaTripDetails( $network, $trip_id ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

            if ( $trip_id ) {
                
                try {
    
                    $db  = new SQLite3( $SqliteDb );
                    
                    $sql = sprintf( "SELECT ptna_is_invalid,ptna_is_wrong,ptna_comment 
                                     FROM   trips 
                                     WHERE  trip_id='%s';", 
                                     SQLite3::escapeString($trip_id)
                                  );
                    
                    $row = $db->querySingle( $sql, true );

                    return $row;
                    
                } catch ( Exception $ex ) {
                    echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
                }
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return array();
    }
    

    function CreatePtnaDetails( $network ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

            try {

                $start_time = gettimeofday(true);
                    
                $db  = new SQLite3( $SqliteDb );
                
                $sql = sprintf( "SELECT * FROM ptna ;" );
                
                $ptna = $db->querySingle( $sql, true );

                $sql        = "SELECT * FROM feed_info";
                
                $feed       = $db->querySingle( $sql, true );
                
                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Network / Coverage</td>' . "\n";
                if ( $ptna["network_name_url"] ) {
                    echo '                            <td class="gtfs-text"><a target="_blank" href="' . $ptna["network_name_url"] . '">' . htmlspecialchars($ptna["network_name"]) . '</a></td>' . "\n";
                } else {
                    echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["network_name"]) . '</td>' . "\n";
                }
                echo '                        </tr>' . "\n";
                
                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Comment on data</td>' . "\n";
                if ( $ptna["comment"] ) {
                    echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["comment"])  . '</td>' . "\n";
                } else {
                    echo '                            <td class="gtfs-text"></td>' . "\n";
                }
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Feed Publisher</td>' . "\n";
                if ( $feed["feed_publisher_name"] ) {
                    if ( $feed["feed_publisher_url"] ) {
                        echo '                            <td class="gtfs-text"><a target="_blank" href="' . $feed["feed_publisher_url"] . '" title="From GTFS">' . htmlspecialchars($feed["feed_publisher_name"]) . '</a></td>' . "\n";
                    } else {
                        echo '                            <td class="gtfs-text">' . htmlspecialchars($feed["feed_publisher_name"]) . '</td>' . "\n";
                    }
                } elseif ( $ptna["feed_publisher_name"] ) {
                    if ( $ptna["feed_publisher_url"] ) {
                        echo '                            <td class="gtfs-text"><a target="_blank" href="' . $ptna["feed_publisher_url"] . '" title="From PTNA">' . htmlspecialchars($ptna["feed_publisher_name"]) . '</a></td>' . "\n";
                    } else {
                        echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["feed_publisher_name"]) . '</td>' . "\n";
                    }
                } else {
                    echo '                            <td class="gtfs-text">&nbsp;</td>' . "\n";
                }
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Feed Start Date</td>' . "\n";
                if ( preg_match( "/^(\d{4})(\d{2})(\d{2})$/", $feed["feed_start_date"], $parts ) ) {
                    echo '                            <td class="gtfs-text">' . $parts[1] . '-' .  $parts[2] . '-' .  $parts[3] . '</td>' . "\n";
                } else {
                    echo '                            <td class="gtfs-text">' . htmlspecialchars($feed["feed_start_date"]) . '</td>' . "\n";
                }
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Feed End Date</td>' . "\n";
                if ( preg_match( "/^(\d{4})(\d{2})(\d{2})$/", $feed["feed_end_date"], $parts ) ) {
                    echo '                            <td class="gtfs-text">' . $parts[1] . '-' .  $parts[2] . '-' .  $parts[3] . '</td>' . "\n";
                } else {
                    echo '                            <td class="gtfs-text">' . htmlspecialchars($feed["feed_end_date"]) . '</td>' . "\n";
                }
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Feed Version</td>' . "\n";
                echo '                            <td class="gtfs-text">' . htmlspecialchars($feed["feed_version"]) . '</td>' . "\n";
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Download Date</td>' . "\n";
                echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["release_date"]) . '</td>' . "\n";
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Dowload Url</td>' . "\n";
                if ( $ptna["release_url"] ) {
                    echo '                            <td class="gtfs-text"><a target="_blank" href="' . $ptna["release_url"] . '">' . htmlspecialchars($ptna["release_url"]) . '</a></td>' . "\n";
                } else {
                    echo '                            <td class="gtfs-text">&nbsp;</td>' . "\n";
                }
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Publisher\'s License</td>' . "\n";
                if ( $ptna["original_license_url"] ) {
                    echo '                            <td class="gtfs-text"><a target="_blank" href="' . $ptna["original_license_url"] . '">' . htmlspecialchars($ptna["original_license"]) . '</a></td>' . "\n";
                } else {
                    echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["original_license"]) . '</td>' . "\n";
                }
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">License given for use in OSM</td>' . "\n";
                if ( $ptna["license_url"] ) {
                    echo '                            <td class="gtfs-text"><a target="_blank" href="' . $ptna["license_url"] . '">' . htmlspecialchars($ptna["license"]) . '</a></td>' . "\n";
                } else {
                    echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["license"]) . '</td>' . "\n";
                }
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">Has Shape Data</td>' . "\n";
                if ( $ptna["has_shapes"] ) {
                    echo '                           <td class="gtfs-text"><img src="/img/CheckMark.svg" width=32 height=32 alt="yes" /></td>' . "\n";
                } else {
                    echo '                           <td class="gtfs-text"></td>' . "\n";
                }
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">GTFS data prepared for PTNA</td>' . "\n";
                echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["prepared"])   . '</td>' . "\n";
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">GTFS data aggregated for PTNA</td>' . "\n";
                echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["aggregated"]) . '</td>' . "\n";
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">GTFS data analyzed for PTNA</td>' . "\n";
                echo '                            <td class="gtfs-text">' . htmlspecialchars($ptna["analyzed"])   . '</td>' . "\n";
                echo '                        </tr>' . "\n";

                echo '                        <tr class="statistics-tablerow">' . "\n";
                echo '                            <td class="gtfs-name">GTFS data normalized for PTNA</td>' . "\n";
                if ( $ptna["normalized"] ) {
                    echo '                            <td class="gtfs-text"><a href="/en/gtfs-statistics.php?network=' .  urlencode($network) . '">' . htmlspecialchars($ptna["normalized"])  . '</a></td>' . "\n";
                } else {
                    echo '                            <td class="gtfs-text"></td>' . "\n";
                }
                echo '                        </tr>' . "\n";

                $stop_time = gettimeofday(true);
                
                return $stop_time - $start_time;
        
            } catch ( Exception $ex ) {
                echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return 0;
    }


    function CreatePtnaAggregationStatistics( $network ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

            try {

                $start_time = gettimeofday(true);
                    
                $db  = new SQLite3( $SqliteDb );
                
                $sql = sprintf( "SELECT * FROM ptna_aggregation;" );
                
                $ptna = $db->querySingle( $sql, true );
                
                if ( $ptna["date"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Date</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . htmlspecialchars($ptna["date"]) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[YYYY-MM-DD]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["duration"] ) {
                    $duration = $ptna["duration"];
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Duration</td>' . "\n";
                    echo '                            <td class="statistics-number">';
                    $format_mins = '%2d:';
                    $format_secs = '%2d';
                    if ( $duration > 3600 ) {
                        printf( "%2d:", $duration / 3600 );
                        $format_mins = '%02d:';
                    }
                    if ( $duration > 60 ) {
                        printf( $format_mins, ($duration % 3600) / 60 );
                        $format_secs = '%02d';
                    }
                    printf( $format_secs, ($duration % 60) );
                    echo '                            <td class="statistics-number">[hh:mm:ss]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["size_before"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">SQLite-DB size before</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . sprintf( "%2.2f", htmlspecialchars($ptna["size_before"]) / 1024 / 1024 ) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[MB]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["size_after"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">SQLite-DB size after</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . sprintf( "%2.2f", htmlspecialchars($ptna["size_after"]) / 1024 / 1024 ) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[MB]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["routes_before"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Number of Routes before</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . sprintf( "%2d", htmlspecialchars($ptna["routes_before"]) ) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[1]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["routes_after"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Number of Routes after</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . sprintf( "%2d", htmlspecialchars($ptna["routes_after"]) ) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[1]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["trips_before"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Number of Trips before</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . sprintf( "%2d", htmlspecialchars($ptna["trips_before"]) ) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[1]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["trips_after"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Number of Trips after</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . sprintf( "%2d", htmlspecialchars($ptna["trips_after"]) ) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[1]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["stop_times_before"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Number of Stop-Times before</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . sprintf( "%2d", htmlspecialchars($ptna["stop_times_before"]) ) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[1]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["stop_times_after"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Number of Stop-Times after</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . sprintf( "%2d", htmlspecialchars($ptna["stop_times_after"]) ) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[1]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                
                $stop_time = gettimeofday(true);
                
                return $stop_time - $start_time;
        
            } catch ( Exception $ex ) {
                echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return 0;
    }
    

    function CreatePtnaAnalysisStatistics( $network ) {

        $SqliteDb = FindGtfsSqliteDb( $network );
        
        if ( $SqliteDb != '' ) {

            try {

                $start_time = gettimeofday(true);
                    
                $db  = new SQLite3( $SqliteDb );
                
                $sql = sprintf( "SELECT * FROM ptna_analysis;" );
                
                $ptna = $db->querySingle( $sql, true );
                
                if ( $ptna["date"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Date</td>' . "\n";
                    echo '                            <td class="statistics-date">'  . htmlspecialchars($ptna["date"]) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[YYYY-MM-DD]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["duration"] ) {
                    $duration = $ptna["duration"];
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Duration</td>' . "\n";
                    echo '                            <td class="statistics-number">';
                    $format_mins = '%2d:';
                    $format_secs = '%2d';
                    if ( $duration > 3600 ) {
                        printf( "%2d:", $duration / 3600 );
                        $format_mins = '%02d:';
                    }
                    if ( $duration > 60 ) {
                        printf( $format_mins, ($duration % 3600) / 60 );
                        $format_secs = '%02d';
                    }
                    printf( $format_secs, ($duration % 60) );
                    echo '                            <td class="statistics-number">[hh:mm:ss]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }
                if ( $ptna["count_subroute"] ) {
                    echo '                        <tr class="statistics-tablerow">' . "\n";
                    echo '                            <td class="statistics-name">Sub-Routes</td>' . "\n";
                    echo '                            <td class="statistics-number">'  . htmlspecialchars($ptna["count_subroute"]) . '</td>' . "\n";
                    echo '                            <td class="statistics-number">[1]</td>' . "\n";
                    echo '                        </tr>' . "\n";
                }

                $stop_time = gettimeofday(true);
                
                return $stop_time - $start_time;
        
            } catch ( Exception $ex ) {
                echo "Sqlite DB could not be opened: " . $ex->getMessage() . "\n";
            }
        } else {
            echo "Sqlite DB not found for network = '" . $network . "'\n";
        }
        
        return 0;
    }
    
?>