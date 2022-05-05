/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50731
 Source Host           : localhost:3306
 Source Schema         : laravel-collector

 Target Server Type    : MySQL
 Target Server Version : 50731
 File Encoding         : 65001

 Date: 05/05/2022 14:36:10
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for game_match_rounds
-- ----------------------------
DROP TABLE IF EXISTS `game_match_rounds`;
CREATE TABLE `game_match_rounds`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` int(10) UNSIGNED NOT NULL COMMENT '游戏',
  `match_id` int(10) UNSIGNED NOT NULL COMMENT '比赛',
  `sign` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标识',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态',
  `team_a` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '队伍A',
  `team_b` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '队伍B',
  `score_a` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '队伍A得分',
  `score_b` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '队伍B得分',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `game_id`(`game_id`, `match_id`, `sign`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8207 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '比赛安排' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for game_matches
-- ----------------------------
DROP TABLE IF EXISTS `game_matches`;
CREATE TABLE `game_matches`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` int(10) UNSIGNED NOT NULL COMMENT '游戏',
  `sign` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标识',
  `name` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `score` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '比分',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态',
  `team_a` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '队伍A',
  `team_b` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '队伍B',
  `score_a` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '队伍A得分',
  `score_b` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '队伍B得分',
  `start_at` timestamp(0) NULL DEFAULT NULL COMMENT '开始时间',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `game_id`(`game_id`, `sign`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1535 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '比赛安排' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for game_rules
-- ----------------------------
DROP TABLE IF EXISTS `game_rules`;
CREATE TABLE `game_rules`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` int(10) UNSIGNED NOT NULL COMMENT '游戏ID',
  `mapping` json NOT NULL COMMENT '映射配置',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for games
-- ----------------------------
DROP TABLE IF EXISTS `games`;
CREATE TABLE `games`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel` int(10) UNSIGNED NOT NULL COMMENT '频道',
  `sign` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标识',
  `name` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `sub_name` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '子名称',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态',
  `start_at` date NULL DEFAULT NULL COMMENT '开始时间',
  `end_at` date NULL DEFAULT NULL COMMENT '结束时间',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `channel`(`channel`, `sign`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '比赛' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
