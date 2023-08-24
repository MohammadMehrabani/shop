#!/bin/bash

DELAY=10

mongo <<EOF
var config = {
    "_id": "dbrs",
    "version": 1,
    "members": [
        {
            "_id": 1,
            "host": "mongo_primary:27017",
            "priority": 2
        },
        {
            "_id": 2,
            "host": "mongo_secondary_1:27017",
            "priority": 1
        },
        {
            "_id": 3,
            "host": "mongo_secondary_2:27017",
            "priority": 1
        }
    ]
};
rs.initiate(config, { force: true });
EOF

echo "****** Waiting for ${DELAY} seconds for replicaset configuration to be applied ******"

sleep $DELAY

echo "ok"
