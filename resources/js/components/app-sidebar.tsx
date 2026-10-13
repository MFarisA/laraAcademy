import { Link } from '@inertiajs/react';
import {
    BookOpen,
    Building2,
    CalendarDays,
    ClipboardList,
    FileText,
    GraduationCap,
    Laptop,
    Layers,
    LayoutGrid,
    ListChecks,
    School,
    UserCog,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
// import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as batchesIndex } from '@/routes/batches';
import { index as branchesIndex } from '@/routes/branches';
import { index as cbtSessionsIndex } from '@/routes/cbt/sessions';
import { index as classroomsIndex } from '@/routes/classrooms';
import { index as examSessionsIndex } from '@/routes/exam-sessions';
import { index as examTemplatesIndex } from '@/routes/exam-templates';
import { index as programsIndex } from '@/routes/programs';
import { index as questionsIndex } from '@/routes/questions';
import { index as schedulesIndex } from '@/routes/schedules';
import { index as subjectsIndex } from '@/routes/subjects';
import { index as usersIndex } from '@/routes/users';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Ujian CBT',
        href: cbtSessionsIndex(),
        icon: Laptop,
    },
    {
        title: 'Bank Soal',
        href: questionsIndex(),
        icon: ListChecks,
    },
    {
        title: 'Template Ujian',
        href: examTemplatesIndex(),
        icon: FileText,
    },
    {
        title: 'Sesi Ujian',
        href: examSessionsIndex(),
        icon: ClipboardList,
    },
    {
        title: 'Mata Pelajaran',
        href: subjectsIndex(),
        icon: BookOpen,
    },
    {
        title: 'Program',
        href: programsIndex(),
        icon: GraduationCap,
    },
    {
        title: 'Angkatan',
        href: batchesIndex(),
        icon: Layers,
    },
    {
        title: 'Kelas',
        href: classroomsIndex(),
        icon: School,
    },
    {
        title: 'Cabang',
        href: branchesIndex(),
        icon: Building2,
    },
    {
        title: 'Jadwal Kelas',
        href: schedulesIndex(),
        icon: CalendarDays,
    },
    {
        title: 'Pengguna',
        href: usersIndex(),
        icon: UserCog,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
