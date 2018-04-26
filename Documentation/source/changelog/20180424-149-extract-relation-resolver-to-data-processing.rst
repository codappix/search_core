Breaking Change 149 "Extract RelationResolver to a new DataProcessor"
=====================================================================

The resolving of relation, based on TCA, is no longer done by the indexer. Instead we
now provide a DataProcessor to solve this job.

As this makes it necessary to configure the DataProcessor, this is a breaking change.
Before the resolving was done out of the box.

So why did we change that? The resolving of relations was already implemented before
we added the data processors. As the concept of data processors is far more flexible,
we try to migrate hard coupled components step by step. The benefit of this change is
basically that you can now configure the resolving of relations and far more
important, the order of execution.

Now it's possible to first copy some fields, e.g. ``starttime`` and ``endtime`` to
further fields and to resolve relations afterwards. As the copied fields are not
configured in TCA, they will be skipped. This way an integrator can keep certain
information as they are.

Also the processor is now configured as all other processors. You can now optionally
configure fields to not process.

See :issue:`149` and :issue:`147`.
