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

 Date: 02/12/2020 09:00:03
*/


-- ----------------------------
-- Table structure for m_regex_nomor
-- ----------------------------
DROP TABLE IF EXISTS "kependudukan"."m_regex_nomor";
CREATE TABLE "kependudukan"."m_regex_nomor" (
  "id" int4 NOT NULL,
  "regex" varchar(255) COLLATE "pg_catalog"."default",
  "urutan" int2,
  "keterangan" varchar(255) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Records of m_regex_nomor
-- ----------------------------
INSERT INTO "kependudukan"."m_regex_nomor" VALUES (1, '\s\W\s', 1, 'cari char spesial diantara spasi');
INSERT INTO "kependudukan"."m_regex_nomor" VALUES (2, '(?<=[\w|\W])\/(?=\s|,|.)', 2, 'cari slash dengan kondisi sebelumnya berupa char atau spesial char dan selanjutnya berupa spasi titik koma');
INSERT INTO "kependudukan"."m_regex_nomor" VALUES (3, '(?<=[\w|\W])\sNO(?=\s|,|.)[^a-zA-z]', 3, 'cari NO dengan kondisi sebelumnya berupa char atau spesial char dan spasi dan selanjutnya berupa spasi titik koma dan bukan char');

-- ----------------------------
-- Primary Key structure for table m_regex_nomor
-- ----------------------------
ALTER TABLE "kependudukan"."m_regex_nomor" ADD CONSTRAINT "m_regex_nomor_pkey" PRIMARY KEY ("id");
