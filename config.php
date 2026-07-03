<?php
define('SUPABASE_DB_HOST', getenv('SUPABASE_DB_HOST') ?: 'localhost');
define('SUPABASE_DB_PORT', getenv('SUPABASE_DB_PORT') ?: '5432');
define('SUPABASE_DB_NAME', getenv('SUPABASE_DB_NAME') ?: 'postgres');
define('SUPABASE_DB_USER', getenv('SUPABASE_DB_USER') ?: 'postgres');
define('SUPABASE_DB_PASSWORD', getenv('SUPABASE_DB_PASSWORD') ?: '');