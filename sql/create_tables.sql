SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;

--
-- Database: `spartablog1`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_guid` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `site` varchar(50) DEFAULT NULL,
  `content` text NOT NULL,
  `addDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_guid` (`post_guid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `name` varchar(50) NOT NULL,
  `pubDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` varchar(15) NOT NULL DEFAULT 'buyog',
  `body` text NOT NULL,
  `draft` tinyint(1) NOT NULL DEFAULT '1',
  `standalone` tinyint(1) NOT NULL DEFAULT '0',
  `content_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `guid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `pubDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` varchar(15) NOT NULL DEFAULT 'buyog',
  `body` text NOT NULL,
  `draft` tinyint(1) NOT NULL DEFAULT '1',
  `seoName` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`guid`),
  KEY `title` (`title`,`author`),
  FULLTEXT KEY `title_2` (`title`,`body`),
  FULLTEXT KEY `seoName` (`seoName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=80 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tag` varchar(15) NOT NULL,
  `post_guid` int(11) NOT NULL,
  KEY `tag` (`tag`,`post_guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(64) NOT NULL,
  `password` varchar(128) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

COMMIT;
