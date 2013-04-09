DROP TABLE IF EXISTS "categories";

CREATE TABLE categories (
  id          INTEGER       NOT NULL PRIMARY KEY AUTOINCREMENT, /* Internal ID */
  code        VARCHAR(255)  NOT NULL, /* Code - a custom ID */
  name        VARCHAR(255)  NOT NULL, /* Free text name */
  description VARCHAR(255)  NOT NULL, /* A short description */
  parent_code VARCHAR(255)  NULL, /* If no parent code - the root category for this lang */
  keywords    VARCHAR(1024) NULL, /* Keywords */
  language    VARCHAR(10)   NOT NULL default 'bg', /* Lang */
  status      INTEGER       NOT NULL default '2' /* 0 - draft, 1 - reviewed, 2 - published (only published are visible) */
);

INSERT INTO "categories" VALUES(
  15,
  'category1',
  'Category 1',
  'Category 1',
  '',
  'Default category',
  'en',
  2);

INSERT INTO "categories" VALUES(
  11,
  'category1',
  'Категория 1',
  'Категория 1',
  '',
  'Категория по подразбиране',
  'bg',
  2);

DROP TABLE IF EXISTS "files";

CREATE TABLE "files" (
  id          INTEGER PRIMARY KEY  NOT NULL,
  name        VARCHAR(255) NOT NULL,
  status      INTEGER NOT NULL DEFAULT 1,
  type        INTEGER,
  created_at  DATETIME,
  title       VARCHAR,
  description VARCHAR,
  source      VARCHAR
);

DROP TABLE IF EXISTS "links";

CREATE TABLE links (
  id            INTEGER       NOT NULL PRIMARY KEY AUTOINCREMENT,
  name          VARCHAR(255)  NOT NULL,
  url           VARCHAR(255)  NOT NULL,
  description   VARCHAR(255)  NOT NULL,
  code          VARCHAR(255)  NOT NULL,
  category_code VARCHAR(255)  NOT NULL
);

DROP TABLE IF EXISTS "options";

CREATE TABLE options (
  id            INTEGER       NOT NULL PRIMARY KEY AUTOINCREMENT,
  option_code   VARCHAR(255)  NOT NULL,
  option_value  VARCHAR(255)  NOT NULL,
  language      VARCHAR(2)    NOT NULL,
  option_name   VARCHAR(255)  NOT NULL
);

INSERT INTO "options" VALUES(1,'site_name','Site name','en','site_name');
INSERT INTO "options" VALUES(7,'site_name','Име на сайта','bg','site_name');

DROP TABLE IF EXISTS "pages";

CREATE TABLE pages (
  id            INTEGER       NOT NULL PRIMARY KEY AUTOINCREMENT, /* Internal ID */
  code          VARCHAR(45)   NOT NULL, /* Code - a custom ID */
  title         VARCHAR(255)  NOT NULL, /* Title of the page */
  description   VARCHAR(255)  NOT NULL, /* Description of the page */
  keywords      text          NOT NULL, /* Keywords of the page */
  renderer      VARCHAR(255)  NOT NULL, /* Template ID of the page */
  body          text          NOT NULL, /* Body of the page */
  parent_code   VARCHAR(45)   default NULL, /* Code of parent page */
  language      VARCHAR(8)    NOT NULL, /* Language */
  status        VARCHAR(255)  NOT NULL, /* Status */
  created_at    datetime      NOT NULL, /* Created time */
  updated_at    datetime      NOT NULL /* Updated time */
);

INSERT INTO "pages" VALUES(
  32,
  'root',
  'Hello and Welcome',
  'This is a sample page.',
  'Made in Sofia, Bulgaria',
  'default',
  'This is a sample *root* page. It is used for content for the index page.',
  NULL,
  'en',
  '2',
  '2009-01-30 00:50:20',
  '2009-08-12 09:58:46'
);

INSERT INTO "pages" VALUES(
  74,
  'root',
  'Et harum quidem rerum facilis',
  'Et harum quidem rerum facilis',
  'Et harum quidem rerum facilis',
  'default',
  'Това е примерна *root* страница. Използва се за съдържанието на началната страница.',
  NULL,
  'bg',
  '2',
  '2009-05-07 10:32:29',
  '2012-09-08 22:55:02'
);

INSERT INTO "pages" VALUES(
  33,
  'blog',
  'Blog',
  'Nunc ultrices sollicitudin lacus',
  'Nunc ultrices sollicitudin lacus',
  'default',
  'This is a sample *blog* page. It is used for content for the index page.',
  'root',
  'en',
  '2',
  '2009-01-30 00:50:20',
  '2012-09-08 23:07:37'
);

INSERT INTO "pages" VALUES(
  87,
  'blog',
  'Blog',
  'Blog',
  'Blog',
  'blog',
  'Това е примерна *root* страница. Използва се за съдържанието на началната страница.',
  'root',
  'bg',
  '2',
  '2012-09-08 22:58:50',
  '2012-09-08 22:58:50'
);

DROP TABLE IF EXISTS "pages2files";

CREATE TABLE "pages2files" ("page_id" INTEGER NOT NULL , "file_id" INTEGER NOT NULL , PRIMARY KEY ("page_id", "file_id"));

DROP TABLE IF EXISTS "posts";

CREATE TABLE posts (
  id            INTEGER       NOT NULL PRIMARY KEY AUTOINCREMENT,
  user_id       int(11)       NOT NULL,
  title         VARCHAR(255)  NOT NULL,
  keywords      VARCHAR(255)  default NULL,
  body          text          NOT NULL,
  created_at    DATETIME      NOT NULL,
  updated_at    DATETIME      NOT NULL,
  language      VARCHAR(8)    NOT NULL default 'bg',
  status        int(10)       NOT NULL,
  description   VARCHAR(255)  NOT NULL,
  published_at  DATETIME      NOT NULL
);

INSERT INTO "posts" VALUES(116,1,'Ut enim ad minima veniam','Ut enim ad minima veniam','<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>','2009-07-04 03:08:23','2012-09-08 23:01:44','bg',2,'Ut enim ad minima veniam','2009-07-04 03:08:23');
INSERT INTO "posts" VALUES(127,1,'Quisque ornare consequat ante laoreet ullamcorper','Quisque ornare consequat ante laoreet ullamcorper','<p>Nullam at enim velit, at volutpat lacus. Morbi pellentesque elementum mi. Nullam vel massa malesuada massa congue mattis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam vel eros in felis gravida mollis. Quisque interdum leo ac tortor commodo congue. Aliquam velit mauris, varius quis placerat a, dapibus eget augue. Quisque ornare consequat ante laoreet ullamcorper. Curabitur et aliquet urna.</p>','2009-08-21 11:43:16','2012-09-08 23:08:30','en',2,'Quisque ornare consequat ante laoreet ullamcorper','2009-08-21 11:43:16');

DROP TABLE IF EXISTS "posts2files";

CREATE TABLE "posts2files" ("post_id" INTEGER NOT NULL , "file_id" INTEGER NOT NULL , PRIMARY KEY ("post_id", "file_id"));

DROP TABLE IF EXISTS "posts2post_categories";

CREATE TABLE posts2post_categories (
  id                INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  post_id int(11)   NOT NULL,
  post_category_id  int(11) NOT NULL
);

INSERT INTO "posts2post_categories" VALUES(537,116,11);
INSERT INTO "posts2post_categories" VALUES(538,127,15);

DROP TABLE IF EXISTS "users";

CREATE TABLE users (
  id        INTEGER       NOT NULL PRIMARY KEY AUTOINCREMENT,
  username  VARCHAR(45)   NOT NULL,
  password  VARCHAR(255)  NOT NULL,
  firstname VARCHAR(45)   NOT NULL,
  lastname  VARCHAR(45)   NOT NULL,
  phone     VARCHAR(45)   NOT NULL,
  email     VARCHAR(45)   NOT NULL,
  nick      VARCHAR(255)  NOT NULL
);

INSERT INTO "users" VALUES(
  1,
  'admin', /* default user name */
  '2ac9cb7dc02b3c0083eb70898e549b63', /* md5 hash of 'Password1' */
  'FirstName', /* default first name */
  'LastName', /* default last name */
  '555', /* default phone */
  'admin@example.com', /* default email */
  'Admin'); /* default nick name */

CREATE INDEX "catid"  ON "categories" ("id");
CREATE INDEX "lid"    ON "links" ("id");
CREATE INDEX "oid"    ON "options" ("id");
CREATE INDEX "pid"    ON "pages" ("id");
CREATE INDEX "poid"   ON "posts" ("id");
CREATE INDEX "ppcid"  ON "posts2post_categories" ("id");
CREATE INDEX "uid"    ON "users" ("id");

DROP TABLE IF EXISTS "dblog";

CREATE TABLE dblog (
    version INTEGER
);

INSERT INTO "dblog" VALUES(1);
