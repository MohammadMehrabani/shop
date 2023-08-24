#!/bin/bash

mongo <<EOF
rs.status();
use admin;
db.createUser({user: 'admin', pwd: 'secret', roles: [ { role: 'root', db: 'admin' } ]});
EOF
