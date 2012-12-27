#!/bin/bash

DB="t4mpsr"
TENANTTBL="tenants"
INCOMETBL="incomes"
SPARETBL="spares"
PAYTBL="payments"
JTBL="joints"
GPAY="group_pay"
GINC="group_inc"
TBAL="balance_per_tenants"
TGROUP="tenant_group"

cat <<EOF

drop table if exists ${JTBL};
create table ${JTBL} (
 id int auto_increment primary key,
 name varchar(30)
);

drop table if exists ${TENANTTBL};

create table ${TENANTTBL} (
 id int auto_increment primary key,
 jointid int not null default 0,
 name varchar(30) NOT NULL,
 active bool
);

drop table if exists ${TGROUP};

create table ${TGROUP} (
 id int auto_increment primary key,
 name varchar(30) NOT NULL
);

insert into ${TENANTTBL} (name,active) values ("Hajdu Gyula", true);
insert into ${TENANTTBL} (name,active) values ("Jose", true);
insert into ${TENANTTBL} (name,active) values ("Dude", true);

drop table ${INCOMETBL};

/* SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'; */

/*
Incomes
id: transaction id
tid: tenant id (only tenants can pay hence the constraint)
datetime: obvious
amount: amount - if negative it means the money moved to the spares table
        in this case the income.id must be in the iid in the spares 
        (see spares)
*/
create table ${INCOMETBL} (
 id int NOT NULL auto_increment primary key,
 tid int,
 datetime DATETIME,
 amount int ,
 foreign key (tid) references ${TENANTTBL}(id) );

drop table ${SPARETBL};
/*
Spares
id: transaction id
iid: income id - all spares were incomes once, moving fund
     from income to spares generates a negative payment in
     income and a positive in the spares.
amount: guess what
*/
create table ${SPARETBL} (
 id int NOT NULL auto_increment key,
 iid int,
 datetime DATETIME,
 amount int ,
 foreign key (iid) references ${INCOMETBL}(id) );

drop table if exists ${PAYTBL};

/*
Payments
id: transaction id
tid: tenant have to pay this much
amount: guess what
*/
create table ${PAYTBL} (
 id int NOT NULL auto_increment primary key,
 tid int,
 datetime DATETIME,
 comment varchar(30),
 amount int ,
 foreign key (tid) references ${TENANTTBL}(id) );


insert into ${INCOMETBL} (tid,amount) values (1,1000);
insert into ${INCOMETBL} (tid,amount) values (1,11000);
insert into ${INCOMETBL} (tid,amount) values (1,5000);
insert into ${INCOMETBL} (tid,amount) values (1,2340);
insert into ${INCOMETBL} (tid,amount) values (2,330);
insert into ${INCOMETBL} (tid,amount) values (2,3210);
insert into ${INCOMETBL} (tid,amount) values (2,1550);
insert into ${INCOMETBL} (tid,amount) values (2,330);
insert into ${INCOMETBL} (tid,amount) values (3,1330);
insert into ${INCOMETBL} (tid,amount) values (3,210);
insert into ${INCOMETBL} (tid,amount) values (3,550);
insert into ${INCOMETBL} (tid,amount) values (2,390);

insert into ${PAYTBL} (tid,amount) values (1,-390);
insert into ${PAYTBL} (tid,amount) values (2,-390);
insert into ${PAYTBL} (tid,amount) values (3,-390);

insert into ${PAYTBL} (tid,amount) values (1,-1900);
insert into ${PAYTBL} (tid,amount) values (2,-1900);
insert into ${PAYTBL} (tid,amount) values (3,-1900);

insert into ${PAYTBL} (tid,amount) values (1,-1000);
insert into ${PAYTBL} (tid,amount) values (2,-1000);
insert into ${PAYTBL} (tid,amount) values (3,-1000);

create or replace view ${GPAY} as  select tid,sum(amount) as amount from ${PAYTBL} group by tid;
create or replace view ${GINC} as  select tid,sum(amount) as amount from ${INCOMETBL} group by tid;
create or replace view ${TBAL}
       as
       select ${GPAY}.tid,
              ${TENANTTBL}.name,
              ${GINC}.amount + ${GPAY}.amount as balance
       from ${GPAY},${GINC},${TENANTTBL}
       where ${GINC}.tid=${GPAY}.tid
       and
       ${TENANTTBL}.id=${GPAY}.tid;
EOF

exit 0

for i in $(seq 1 50000)
do
 echo "insert into ${INCOMETBL} (tid,amount) values (1,$RANDOM);"
 echo "insert into ${INCOMETBL} (tid,amount) values (2,$RANDOM);"
 echo "insert into ${INCOMETBL} (tid,amount) values (3,$RANDOM);"

 echo "insert into ${PAYTBL} (tid,amount) values (1,-$RANDOM);"
 echo "insert into ${PAYTBL} (tid,amount) values (2,-$RANDOM);"
 echo "insert into ${PAYTBL} (tid,amount) values (3,-$RANDOM);"
done