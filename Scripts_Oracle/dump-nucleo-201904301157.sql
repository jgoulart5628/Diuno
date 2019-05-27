-- MySQL dump 10.16  Distrib 10.1.38-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: nucleo
-- ------------------------------------------------------
-- Server version	10.1.38-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `colunas_long`
--

DROP TABLE IF EXISTS `colunas_long`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `colunas_long` (
  `tabela_id` int(11) NOT NULL,
  `coluna` varchar(30) COLLATE utf8_bin NOT NULL,
  `lidos` int(11) NOT NULL DEFAULT '0',
  `gravados` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`coluna`,`tabela_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colunas_long`
--

LOCK TABLES `colunas_long` WRITE;
/*!40000 ALTER TABLE `colunas_long` DISABLE KEYS */;
INSERT INTO `colunas_long` VALUES (2077,'arq_cancelamento',322,322),(2069,'arq_nfe',6746,6746),(2077,'arq_nfe',59261,59261);
/*!40000 ALTER TABLE `colunas_long` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `converte`
--

DROP TABLE IF EXISTS `converte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `converte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa` varchar(20) COLLATE utf8_bin NOT NULL,
  `tabela` varchar(40) COLLATE utf8_bin NOT NULL,
  `rows_origem` int(11) NOT NULL,
  `rows_destino` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`empresa`,`tabela`)
) ENGINE=InnoDB AUTO_INCREMENT=2332 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `converte`
--

LOCK TABLES `converte` WRITE;
/*!40000 ALTER TABLE `converte` DISABLE KEYS */;
INSERT INTO `converte` VALUES (2185,'deal','adm_anvisa',13416,0),(2186,'deal','adm_empresa',9,0),(2187,'deal','adm_menus',18,0),(2188,'deal','adm_menus_usuario',48,0),(2189,'deal','adm_precos_anv',49473,0),(2190,'deal','adm_rotinas',7,0),(2191,'deal','adm_sess_login',2,0),(2192,'deal','adm_tp_produto_anv',11,0),(2193,'deal','adm_usuario',2,0),(2194,'deal','adm_valida',2798,0),(2195,'deal','auxiliar_52010',6,0),(2196,'deal','auxiliar_52030',10,0),(2197,'deal','auxiliar_52050',3,0),(2198,'deal','auxiliar_52090',8,0),(2199,'deal','auxiliar_52091',14,0),(2200,'deal','auxiliar_52110',248,0),(2201,'deal','auxiliar_52130',1,0),(2202,'deal','auxiliar_52150',67,0),(2203,'deal','auxiliar_52170',3,0),(2204,'deal','auxiliar_52190',3,0),(2205,'deal','auxiliar_52210',5,0),(2206,'deal','auxiliar_52230',1,0),(2207,'deal','auxiliar_52231',5,0),(2208,'deal','auxiliar_52250',1329,0),(2209,'deal','auxiliar_52270',5571,0),(2210,'deal','auxiliar_52290',4,0),(2211,'deal','auxiliar_52310',3,0),(2212,'deal','auxiliar_52330',8,0),(2213,'deal','auxiliar_52350',50,0),(2214,'deal','auxiliar_52370',3,0),(2215,'deal','auxiliar_52390',4,0),(2216,'deal','auxiliar_52410',75,0),(2217,'deal','auxiliar_52430',3,0),(2218,'deal','auxiliar_52450',4,0),(2219,'deal','auxiliar_52470',6,0),(2220,'deal','auxiliar_52490',6,0),(2221,'deal','auxiliar_52510',68,0),(2222,'deal','auxiliar_52530',4,0),(2223,'deal','auxiliar_52550',7,0),(2224,'deal','auxiliar_52570',28,0),(2225,'deal','auxiliar_52590',7,0),(2226,'deal','auxiliar_52610',10,0),(2227,'deal','auxiliar_52630',3,0),(2228,'deal','auxiliar_52650',16,0),(2229,'deal','auxiliar_52670',4,0),(2230,'deal','auxiliar_52690',19,0),(2231,'deal','auxiliar_52710',6,0),(2232,'deal','auxiliar_52730',486,0),(2233,'deal','auxiliar_52750',203,0),(2234,'deal','auxiliar_52770',6,0),(2235,'deal','auxiliar_52790',86,0),(2236,'deal','auxiliar_52810',4,0),(2237,'deal','auxiliar_52830',5,0),(2238,'deal','auxiliar_52850',10,0),(2239,'deal','auxiliar_52870',20,0),(2240,'deal','auxiliar_52890',3,0),(2241,'deal','auxiliar_52910',2556,0),(2242,'deal','financeiro_31070',1,0),(2243,'deal','financeiro_31071',1,0),(2244,'deal','financeiro_31073',1,0),(2245,'deal','financeiro_31075',1,0),(2246,'deal','financeiro_31077',1,0),(2247,'deal','financeiro_31090',1,0),(2248,'deal','financeiro_31091',71,0),(2249,'deal','movim_00010',6,0),(2250,'deal','movim_00030',23,0),(2251,'deal','movim_00033',21,0),(2252,'deal','movim_00050',24,0),(2253,'deal','movim_00051',24,0),(2254,'deal','movim_00053',24,0),(2255,'deal','movim_00070',6,0),(2256,'deal','movim_00071',6,0),(2257,'deal','movim_00090',2,0),(2258,'deal','movim_00110',27,0),(2259,'deal','movim_01051',16,0),(2260,'deal','movim_01090',257,0),(2261,'deal','parametro_51010',1,0),(2262,'deal','parametro_51030',6,0),(2263,'deal','parametro_51050',2,0),(2264,'deal','parametro_51090',24,0),(2265,'deal','parametro_51110',94,0),(2266,'deal','parametro_51130',89,0),(2267,'deal','parametro_51150',3,0),(2268,'deal','pessoa_41010',429,0),(2269,'deal','pessoa_41011',4,0),(2270,'deal','pessoa_41013',4,0),(2271,'deal','pessoa_41015',4,0),(2272,'deal','pessoa_41017',4,0),(2273,'deal','pessoa_41019',4,0),(2274,'deal','pessoa_41031',4,0),(2275,'deal','pessoa_41033',168,0),(2276,'deal','pessoa_41035',4,0),(2277,'deal','pessoa_41037',4,0),(2278,'deal','pessoa_41039',4,0),(2279,'deal','pessoa_41041',1,0),(2280,'deal','pessoa_41050',1,0),(2281,'deal','pessoa_41070',413,0),(2282,'deal','pessoa_41090',413,0),(2283,'deal','pessoa_41110',1,0),(2284,'deal','pessoa_41111',1,0),(2285,'deal','pessoa_41112',2,0),(2286,'deal','pessoa_41113',2,0),(2287,'deal','pessoa_41114',1,0),(2288,'deal','pessoa_41115',2,0),(2289,'deal','pessoa_41116',1,0),(2290,'deal','pessoa_41117',1,0),(2291,'deal','pessoa_41130',1,0),(2292,'deal','pessoa_41250',1,0),(2293,'deal','pessoa_41251',1,0),(2294,'deal','pessoa_41253',1,0),(2295,'deal','pessoa_41255',1,0),(2296,'deal','pessoa_41270',10,0),(2297,'deal','preco_43010',1,0),(2298,'deal','preco_43030',5,0),(2299,'deal','preco_43050',23506,0),(2300,'deal','preco_43070',1,0),(2301,'deal','preco_43090',1,0),(2302,'deal','preco_43110',1,0),(2303,'deal','produto_42010',55985,0),(2304,'deal','produto_42030',110987,0),(2305,'deal','produto_42050',1,0),(2306,'deal','produto_42070',1,0),(2307,'deal','produto_42090',1,0),(2308,'deal','produto_42110',1,0),(2309,'deal','produto_42111',12,0),(2310,'deal','produto_42130',1,0),(2311,'deal','produto_42150',1,0),(2312,'deal','produto_42170',1,0),(2313,'deal','tributario_53010',1177,0),(2314,'deal','tributario_53030',3,0),(2315,'deal','tributario_53050',7,0),(2316,'deal','tributario_53070',527,0),(2317,'deal','tributario_53071',13,0),(2318,'deal','tributario_53090',1,0),(2319,'deal','tributario_53091',9,0),(2320,'deal','tributario_53093',180,0),(2321,'deal','tributario_53110',5,0),(2322,'deal','tributario_53130',2,0),(2323,'deal','tributario_53131',16,0),(2324,'deal','tributario_53150',543,0),(2325,'deal','tributario_53170',16,0),(2326,'deal','tributario_53190',83,0),(2327,'deal','tributario_53210',17,0),(2328,'deal','venda_12010',3,0),(2329,'deal','venda_12030',18,0),(2330,'deal','venda_12070',2,0),(2331,'deal','venda_12090',2,0);
/*!40000 ALTER TABLE `converte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `erro_sql`
--

DROP TABLE IF EXISTS `erro_sql`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erro_sql` (
  `chave` int(11) NOT NULL AUTO_INCREMENT,
  `banco_cliente` varchar(30) COLLATE utf8_bin NOT NULL,
  `data_exe` datetime DEFAULT NULL,
  `erro` int(10) NOT NULL DEFAULT '0',
  `msg` mediumtext COLLATE utf8_bin,
  `sql_exe` longtext COLLATE utf8_bin,
  PRIMARY KEY (`chave`),
  KEY `banco_cliente` (`banco_cliente`,`data_exe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `erro_sql`
--

LOCK TABLES `erro_sql` WRITE;
/*!40000 ALTER TABLE `erro_sql` DISABLE KEYS */;
/*!40000 ALTER TABLE `erro_sql` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'nucleo'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-04-30 11:57:04
