ALTER TABLE `volunteerteachers` MODIFY `eidikothta` int(11) UNSIGNED;
ALTER TABLE `volunteerteachers` ADD CONSTRAINT `fk_branch_id` FOREIGN KEY (`eidikothta`) REFERENCES `branch`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
