/*
 Navicat Premium Data Transfer

 Source Server         : local-postgre
 Source Server Type    : PostgreSQL
 Source Server Version : 100013
 Source Host           : localhost:5432
 Source Catalog        : normalisasi_alamat
 Source Schema         : kependudukan

 Target Server Type    : PostgreSQL
 Target Server Version : 100013
 File Encoding         : 65001

 Date: 02/12/2020 08:59:45
*/


-- ----------------------------
-- Table structure for m_regex_gang
-- ----------------------------
DROP TABLE IF EXISTS "kependudukan"."m_regex_gang";
CREATE TABLE "kependudukan"."m_regex_gang" (
  "id" int4 NOT NULL,
  "regex" varchar(255) COLLATE "pg_catalog"."default",
  "urutan" int2,
  "keterangan" varchar(255) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Records of m_regex_gang
-- ----------------------------
INSERT INTO "kependudukan"."m_regex_gang" VALUES (4, 'BLOK', 1, 'cari blok');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (3, 'BLOK\s', 2, 'cari blok dengan spasi');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (7, 'BLK', 4, 'cari blk');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (6, 'BLK\s', 5, 'cari blk dengan spasi');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (11, 'GG-', 7, 'cari gg dengan dash');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (10, 'GG\s', 8, 'cari gg dengan spasi');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (9, 'GG\s-', 9, 'cari gg dengan spasi dash');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (14, 'GANG-', 11, 'cari gang dengan dash');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (13, 'GANG\s', 12, 'cari gang dengan spasi');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (2, 'BLOK(?=\s|-|\/)[^a-zA-z]', 3, 'cari blok dengan kondisi selanjutnya anchor(spasi atau dash atau slash) kemudian selanjutnya tanpa huruf');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (5, 'BLK(?=\s|-|\/)[^a-zA-z]', 6, 'cari blk dengan kondisi selanjutnya anchor (spasi atau dash atau slash) kemudian selanjutnya tanpa huruf');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (8, 'GG(?=\s|-|\/)[^a-zA-z]', 10, 'cari gg dengan kondisi selanjutnya anchor (spasi atau dash atau slash) kemudian selanjutnya tanpa huruf');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (12, 'GANG(?=\s|-|\/)[^a-zA-z]', 13, 'cari gang dengan kondisi selanjutnya anchor (spasi atau dash atau slash) kemudian selanjutnya tanpa huruf');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (1, '(?<=[^\d|\W])\s(?=\d\s)', 14, 'cari spasi dengan kondisi sbelumnya anchor (tidak mengandung angka dan spesial karakter) dan selanjutnya anchor (angka dan spasi)');
INSERT INTO "kependudukan"."m_regex_gang" VALUES (15, '(?<=[^\d|\W])\s(?=\d|\s|\w\d|\w\W)', 15, 'cari spasi dengan kondisi sbelumnya anchor (tidak mengandung angka dan spesial karakter) dan selanjutnya anchor (angka atau spasi atau huruf dan angka atau huruf dan spesial karakter)');

-- ----------------------------
-- Primary Key structure for table m_regex_gang
-- ----------------------------
ALTER TABLE "kependudukan"."m_regex_gang" ADD CONSTRAINT "regex_gang_pkey" PRIMARY KEY ("id");
