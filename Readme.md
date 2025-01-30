# PG-select-to-insert 

This is a tiny program meant to allow you to pass a filename which contains a record which you can then turn into an insert statement. 

The work flow would look like this:

- `psql somedb`
- `\x` - get the extended view 
- `select * from table limit 1`;
- Copy and paste into a file or, if using the pager press "s" and save to a file that way. 
- `./pg-select-to-insert.php filename tablename` (optionally redirecting output `> insert.sql` )

Then you have an insert statement which you can manage as you please, because sometimes you just want to insert the one record you are looking at. 
