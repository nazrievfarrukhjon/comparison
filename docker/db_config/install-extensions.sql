CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE EXTENSION fuzzystrmatch;
create extension btree_gin;
create index blacklists_search_keys_names_combo_trg_index on blacklists_search_keys using gin (names_ocmbo gin_trgm_ops);
create index whitelists_search_keys_names_combo_trg_index on whitelists_search_keys using gin (names_ocmbo gin_trgm_ops);
