<?php

/** 
* Creazione
* Database 
*/

// Tabella API
$wpdb->query('CREATE TABLE bing_api
(token varchar(100) NOT NULL)');

// Tabella Mappe
$wpdb->query('CREATE TABLE bing_maps
(ID bigint(20) NOT NULL AUTO_INCREMENT,
maps_name varchar(60) NOT NULL,
maps_shortcode varchar(255) NOT NULL,
PRIMARY KEY (ID))');

// Tabella Categorie
$wpdb->query('CREATE TABLE bing_maps_category
(ID bigint(20) NOT NULL AUTO_INCREMENT,
title varchar(60) NOT NULL,
name_file varchar(255) NOT NULL,
url_icon varchar(255) NOT NULL,
url_marker varchar(255) NOT NULL,
color varchar(60) NOT NULL,
PRIMARY KEY (ID))');

// Tabella Coordinate Punti
$wpdb->query('CREATE TABLE bing_maps_point
(ID bigint(20) NOT NULL AUTO_INCREMENT,
title varchar(255) NOT NULL,
description varchar(255) NOT NULL,
coords mediumtext NOT NULL,
color varchar(60) NOT NULL,
id_cat bigint(20) NOT NULL,
name_cat varchar(60) NOT NULL,
PRIMARY KEY (ID))');

// Tabella ID Mappe
$wpdb->query('CREATE TABLE bing_maps_ID
(ID bigint(20) NOT NULL AUTO_INCREMENT,
ID_Category bigint(20) NOT NULL,
ID_Map bigint(20) NOT NULL,
PRIMARY KEY (ID))');


?>