import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Calendar, FolderGit2, LayoutGrid, Shield,Wrench } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { TeamSwitcher } from '@/components/team-switcher';
import { useTranslations } from '@/hooks/use-translations';
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
import { index as dates } from '@/routes/dates';
import { index as roles } from '@/routes/rols';
import { index as statuses } from '@/routes/statuses';
import type { NavItem } from '@/types';

export function AppSidebar() {
    const page = usePage();
    const { t } = useTranslations();
    const dashboardUrl = page.props.currentTeam
        ? dashboard(page.props.currentTeam.slug)
        : '/';
    const rolesUrl = page.props.currentTeam
        ? roles(page.props.currentTeam.slug)
        : '/';
    const datesUrl = page.props.currentTeam
        ? dates(page.props.currentTeam.slug)
        : '/';
    const statusesUrl = page.props.currentTeam
        ? statuses(page.props.currentTeam.slug)
        : '/';
    const canManageRoles =
        page.props.currentTeam?.role === 'owner' ||
        page.props.currentTeam?.role === 'admin';
    const canManageDates =
        page.props.currentTeam?.role === 'owner' ||
        page.props.currentTeam?.role === 'admin';
    const canManageStatuses =
        page.props.currentTeam?.role === 'owner' ||
        page.props.currentTeam?.role === 'admin';

    const mainNavItems: NavItem[] = [
        {
            title: t('ui.dashboard', 'Dashboard'),
            href: dashboardUrl,
            icon: LayoutGrid,
        },
        ...(canManageDates
            ? [
                  {
                      title: t('ui.dates', 'Dates'),
                      href: datesUrl,
                      icon: Calendar,
                  },
              ]
            : []),
        ...(canManageStatuses
            ? [
                  {
                      title: t('ui.statuses', 'Statuses'),
                      href: statusesUrl,
                      icon: Wrench,
                  },
              ]
            : []),
        ...(canManageRoles
            ? [
                  {
                      title: t('ui.roles', 'Roles'),
                      href: rolesUrl,
                      icon: Shield,
                  },
              ]
            : []),
    ];

    const footerNavItems: NavItem[] = [
        {
            title: 'Repository',
            href: 'https://github.com/laravel/react-starter-kit',
            icon: FolderGit2,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits#react',
            icon: BookOpen,
        },
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboardUrl} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <TeamSwitcher />
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
