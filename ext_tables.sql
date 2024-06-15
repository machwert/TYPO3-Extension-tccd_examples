CREATE TABLE tx_tccdexamples_domain_model_tccd (
	title varchar(255) NOT NULL DEFAULT '',
	description text,
    syllabusdescription text,
	version int(11) DEFAULT '0' NOT NULL,
	link varchar(255) NOT NULL DEFAULT '',
    links varchar(255) NOT NULL DEFAULT '',
	slug varchar(255) NOT NULL DEFAULT '',
	edited int(11) DEFAULT '0' NOT NULL,
    makenewtranslation tinyint(1),
    related_tccds varchar(255) NOT NULL DEFAULT '',
    images varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE tx_tccdexamples_domain_model_tccd (
	categories int(11) unsigned DEFAULT '0' NOT NULL
);
