-- MySQL dump 10.11
--
-- Host: localhost    Database: activerecord
-- ------------------------------------------------------
-- Server version	5.0.51a-3ubuntu5.4
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,ANSI' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table "categories"
--

DROP TABLE IF EXISTS "categories";
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE "categories" (
  "id" int(11) NOT NULL auto_increment,
  "name" varchar(255) default NULL,
  PRIMARY KEY  ("id")
);
SET character_set_client = @saved_cs_client;

--
-- Table structure for table "categorizations"
--

DROP TABLE IF EXISTS "categorizations";
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE "categorizations" (
  "id" int(11) NOT NULL auto_increment,
  "post_id" int(11),
  "category_id" int(11),
  PRIMARY KEY  ("id")
);
SET character_set_client = @saved_cs_client;

--
-- Table structure for table "comments"
--

DROP TABLE IF EXISTS "comments";
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE "comments" (
  "id" int(11) NOT NULL auto_increment,
  "author" varchar(255) default NULL,
  "body" varchar(255) default NULL,
  "post_id" int(11) default NULL,
  PRIMARY KEY  ("id")
);
SET character_set_client = @saved_cs_client;

--
-- Table structure for table "posts"
--

DROP TABLE IF EXISTS "posts";
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE "posts" (
  "id" int(11) NOT NULL auto_increment,
  "author_id" int(11) default NULL,
  "body" varchar(255) default NULL,
  "title" varchar(255) default NULL,
  PRIMARY KEY  ("id")
);
SET character_set_client = @saved_cs_client;

--
-- Table structure for table "prefix_authors"
--

DROP TABLE IF EXISTS "prefix_authors";
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE "prefix_authors" (
  "id" int(11) NOT NULL auto_increment,
  "name" varchar(255) default NULL,
  PRIMARY KEY  ("id")
);
SET character_set_client = @saved_cs_client;

--
-- Table structure for table "slugs"
--

DROP TABLE IF EXISTS "slugs";
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE "slugs" (
  "id" int(11) NOT NULL auto_increment,
  "slug" varchar(255) default NULL,
  "post_id" int(11),
  PRIMARY KEY  ("id")
);
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-02-21  1:49:51
