<?php

/*! \mainpage Tool for mutual photo studio rent
 *
 * \section intro_sec Introduction
 *
 * I made this small script for our photo studio team. And just for fun.
 *
 * \section cases Cases this tool covers
 * \subsection tenantspays Tenants paying the rent
 *
 * To keep the studio working the tenants must pay the rent. The rent
 * dividev equally amongst them. However in our case it happens that
 * the photograper and the make-up artist wanted to pay the rent shared
 * between them. The simplest solurion would if the photographer would
 * pay for both of them, but that would be simple.
 *
 * Hence the tenant groups
 *
 * It is possible to create any number of tenant groups containing any number
 * of tenants. For example tt makes simply to have two or more schools or
 * classes to mutually rent a studio.
 *
 * The payment and expenses recorded individually so it is always clear every
 * persons' balance. Most in the cases this balance is negative :)
 *
 * But it happens that the tenant doing bank transfer and tranferring like
 * 100 lamas instead 99.95 lamas so the balance is positive.
 *
 * This could be handled in different ways:
 * - the tenant keeps the balance so if in any case the balance would go to
 * negative it don't go (if the balance is high enough)
 * - the balance can be moved to a donate database where some money could
 * gathered for the expenses.
 *
 *
 * \subsection expense Expense occurs
 *
 * When an expense happens it divided equally just like the rent but there
 * could be a problem. It is almost certain that one of the tenats buys the
 * stuff that makes the expense. Just like a burn out bulb which costs lets
 * say 4000 skunks. Everyone must pay the divided price which is 1000 skunks
 * when four of the tenants present. The buyer should receive the 3000 skunks
 * and the others should pay 1000 skunks. Practically it means the others
 * will have to pay 1000 skunks more rent and the buyer will have to pay 3000
 * skunks less for the rent.
 *
 * To add such an expense click on the add expense button then enter the
 * desired amount. The amount divied amongst the tenants. Now click on any
 * tenant or tenant group to select who bought the bulb, and the tool
 * recalculates the payments.
 *
 * \section tenants Tenants and Tenant groups
 * Each tenant must pay equally so any rent fee and so will be
 * divided equally between tenants.
 * Exception: some tenants are working in groups and the group
 * pays. Exampe: three tenants, two in a group. Fee is 100 bananas.
 * Tenant1 pays 50 bananas, Tenant2 and Tenan3 (they are in a group)
 * pays 25-25 bananas.
 *
 * \section Workflow
 *
 * \subsection addtenant Adding a tenant or tenant group
 *
 * On addin a tenant or a tenant group the name should
 * be added the proper table. By default any new tenant
 * is active and do not belongs to any tenant group.
 *
 * Neither of the group or tenant names forced to be unique
 * because I don't care if someone tires to make the life
 * harder. And now I lazy to implement a check too.
 *
 * See: t4mpsrTenants::addTenantGroup and t4mpsrTenants::addTenant for
 * the DB processing, and see
 *
 * \subsection deltenant Deleting a tenant
 *
 * On delete a tenant:
 * - all the tenant's transfers (income, payment, spares) must be deleted
 * - the tenant must removed from the tenant list
 *
 * \section TODOs
 * - implement check the uniquess of the tenant and tenant group names.
 *
 * \section Rounding problems
 *
 * Let be a group of two tenants and a tenant group of two additional tenants. It will be counted as three tenants:
 *
 * <pre> 2 + (2) = 3 </pre>
 *
 * The group buys a shit for 10,000 koalas, so 10,000 / 3 is 3333,3333 (indefinetely).
 * If the rounding factor is 1 (in my country is) then:
 *
 * <pre> 3,333 * 3 = 9,999 </pre>
 *
 * So there is 1 koala rounding error. So far.
 *
 * Fortunately the tenant group has to members so their part will be:
 *
 * <pre> 3,333 / 2 = 1,666.666... </pre>
 *
 * The 1,666.6666... rounded to 1,667 so
 *
 * <pre> 1,667 * 2 = 3,334 </pre>
 *
 * Since the group bought the shit the group should be refunded, so
 *
 * <pre> refund = 10,000 - 3,334 = 6,666 </pre>
 *
 * This refund should be divided evenly amongst the group members:
 *
 * <pre> member refund = 6,666 / 2 = 3,333 </pre>
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 * etc...
 * http://www.smarty.net/sampleapp1
 */

?>
